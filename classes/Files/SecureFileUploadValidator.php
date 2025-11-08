<?php

namespace phpCollab\Files;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Secure File Upload Validator
 *
 * Provides comprehensive security validation for file uploads including:
 * - MIME type whitelisting
 * - Extension validation
 * - File size limits
 * - Malicious filename detection
 * - Path traversal prevention
 *
 * @package phpCollab\Files
 */
class SecureFileUploadValidator
{
    /**
     * Allowed MIME types with their corresponding extensions
     * SECURITY: Use strict whitelist approach - only allow what's needed
     */
    const ALLOWED_MIME_TYPES = [
        // Images
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],

        // Documents
        'application/pdf' => ['pdf'],
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
        'application/vnd.ms-powerpoint' => ['ppt'],
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],

        // Text files
        'text/plain' => ['txt'],
        'text/csv' => ['csv'],

        // Archives (be cautious with these)
        'application/zip' => ['zip'],
        'application/x-rar-compressed' => ['rar'],
        'application/x-7z-compressed' => ['7z'],
    ];

    /**
     * Dangerous extensions that should NEVER be allowed
     * SECURITY: This is a blacklist for defense-in-depth
     */
    const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'pht', 'phtml', 'phar',
        'inc', 'asp', 'aspx', 'jsp', 'jspx', 'cgi', 'pl', 'py', 'rb',
        'sh', 'bash', 'bat', 'cmd', 'com', 'exe', 'dll', 'scr', 'vbs',
        'js', 'jar', 'war', 'ear', 'htaccess', 'htpasswd', 'web.config',
        'config', 'ini', 'env', 'sql', 'sqlite', 'db', 'mdb'
    ];

    /**
     * Maximum file size in bytes (default: 10MB)
     */
    const MAX_FILE_SIZE = 10485760; // 10MB

    /**
     * Validate uploaded file for security issues
     *
     * @param UploadedFile $file
     * @param int|null $maxSize Maximum file size in bytes (null = use default)
     * @return array Validation result with 'valid' boolean and 'errors' array
     */
    public static function validate(UploadedFile $file, ?int $maxSize = null): array
    {
        $errors = [];
        $maxSize = $maxSize ?? self::MAX_FILE_SIZE;

        try {
            // 1. Check for upload errors
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $errors[] = self::getUploadErrorMessage($file->getError());
            }

            // 2. Validate file size
            if ($file->getSize() > $maxSize) {
                $errors[] = sprintf(
                    'File size (%s) exceeds maximum allowed size (%s)',
                    self::formatBytes($file->getSize()),
                    self::formatBytes($maxSize)
                );
            }

            // 3. Validate MIME type (actual file content)
            $mimeType = $file->getMimeType();
            if (!self::isAllowedMimeType($mimeType)) {
                $errors[] = sprintf('File type not allowed: %s', $mimeType);
            }

            // 4. Validate file extension
            $clientExtension = strtolower($file->getClientOriginalExtension());
            if (!self::isAllowedExtension($clientExtension)) {
                $errors[] = sprintf('File extension not allowed: .%s', $clientExtension);
            }

            // 5. Check for dangerous extensions (defense-in-depth)
            if (self::isDangerousExtension($clientExtension)) {
                $errors[] = sprintf('Dangerous file extension detected: .%s', $clientExtension);
            }

            // 6. Verify MIME type matches extension
            if (!self::mimeTypeMatchesExtension($mimeType, $clientExtension)) {
                $errors[] = 'File extension does not match file content (possible file type spoofing)';
            }

            // 7. Check for path traversal in filename
            $originalName = $file->getClientOriginalName();
            if (self::containsPathTraversal($originalName)) {
                $errors[] = 'Filename contains path traversal sequences';
            }

            // 8. Check for null bytes (file type confusion attack)
            if (strpos($originalName, "\0") !== false) {
                $errors[] = 'Filename contains null byte';
            }

            // 9. Validate filename has valid characters
            if (!self::hasValidFilename($originalName)) {
                $errors[] = 'Filename contains invalid characters';
            }

        } catch (Exception $e) {
            $errors[] = 'File validation error: ' . $e->getMessage();
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'mime_type' => $mimeType ?? null,
            'extension' => $clientExtension ?? null,
        ];
    }

    /**
     * Check if MIME type is in allowed list
     *
     * @param string $mimeType
     * @return bool
     */
    private static function isAllowedMimeType(string $mimeType): bool
    {
        return array_key_exists($mimeType, self::ALLOWED_MIME_TYPES);
    }

    /**
     * Check if extension is in allowed list
     *
     * @param string $extension
     * @return bool
     */
    private static function isAllowedExtension(string $extension): bool
    {
        foreach (self::ALLOWED_MIME_TYPES as $allowedExtensions) {
            if (in_array($extension, $allowedExtensions, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if extension is in dangerous list
     *
     * @param string $extension
     * @return bool
     */
    private static function isDangerousExtension(string $extension): bool
    {
        return in_array($extension, self::DANGEROUS_EXTENSIONS, true);
    }

    /**
     * Verify that MIME type matches the file extension
     *
     * @param string $mimeType
     * @param string $extension
     * @return bool
     */
    private static function mimeTypeMatchesExtension(string $mimeType, string $extension): bool
    {
        if (!isset(self::ALLOWED_MIME_TYPES[$mimeType])) {
            return false;
        }

        return in_array($extension, self::ALLOWED_MIME_TYPES[$mimeType], true);
    }

    /**
     * Check for path traversal sequences in filename
     *
     * @param string $filename
     * @return bool
     */
    private static function containsPathTraversal(string $filename): bool
    {
        $dangerous = [
            '../', '..\\',
            './', '.\\',
            '%2e%2e%2f', '%2e%2e%5c',  // URL encoded
            '..%2f', '..%5c',
            '%252e%252e%252f', '%252e%252e%255c',  // Double URL encoded
        ];

        $lowerFilename = strtolower($filename);

        foreach ($dangerous as $pattern) {
            if (strpos($lowerFilename, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate filename contains only safe characters
     *
     * @param string $filename
     * @return bool
     */
    private static function hasValidFilename(string $filename): bool
    {
        // Allow: alphanumeric, dots, dashes, underscores, spaces
        // Block: path separators, special characters
        $pattern = '/^[a-zA-Z0-9._\s-]+$/';
        return preg_match($pattern, $filename) === 1;
    }

    /**
     * Generate a secure random filename
     *
     * @param string $originalExtension
     * @param int $length Length of random string (default: 32)
     * @return string
     * @throws Exception
     */
    public static function generateSecureFilename(string $originalExtension, int $length = 32): string
    {
        // Validate extension is safe
        $extension = strtolower($originalExtension);
        if (self::isDangerousExtension($extension)) {
            throw new Exception('Cannot generate filename with dangerous extension');
        }

        // Generate cryptographically secure random filename
        $randomName = bin2hex(random_bytes($length / 2));

        return $randomName . '.' . $extension;
    }

    /**
     * Sanitize filename for safe storage
     *
     * Use this if you want to preserve original filename (not recommended for security)
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path components
        $filename = basename($filename);

        // Remove null bytes
        $filename = str_replace("\0", '', $filename);

        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Remove multiple dots (except before extension)
        $parts = explode('.', $filename);
        if (count($parts) > 2) {
            $extension = array_pop($parts);
            $basename = implode('_', $parts);
            $filename = $basename . '.' . $extension;
        }

        // Limit filename length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 250);
            $filename = $basename . '.' . $extension;
        }

        return $filename;
    }

    /**
     * Get upload error message
     *
     * @param int $code
     * @return string
     */
    private static function getUploadErrorMessage(int $code): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
        ];

        return $errors[$code] ?? 'Unknown upload error';
    }

    /**
     * Format bytes to human-readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get list of allowed extensions
     *
     * @return array
     */
    public static function getAllowedExtensions(): array
    {
        $extensions = [];
        foreach (self::ALLOWED_MIME_TYPES as $exts) {
            $extensions = array_merge($extensions, $exts);
        }
        return array_unique($extensions);
    }

    /**
     * Get formatted list of allowed extensions for display to users
     *
     * @return string
     */
    public static function getAllowedExtensionsString(): string
    {
        return implode(', ', self::getAllowedExtensions());
    }
}
