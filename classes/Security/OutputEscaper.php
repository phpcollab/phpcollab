<?php

namespace phpCollab\Security;

use Laminas\Escaper\Escaper;

/**
 * Class OutputEscaper
 *
 * Utility class for proper output escaping to prevent XSS (Cross-Site Scripting) vulnerabilities
 *
 * This class provides context-aware escaping methods for different output contexts:
 * - HTML content
 * - HTML attributes
 * - JavaScript
 * - URLs
 * - CSS
 *
 * SECURITY: ALL user-controlled output MUST be escaped using the appropriate method
 * based on the context where it's being output.
 *
 * @package phpCollab\Security
 */
class OutputEscaper
{
    /** @var Escaper */
    private $escaper;

    /**
     * OutputEscaper constructor.
     *
     * @param Escaper|null $escaper Optional Laminas Escaper instance
     */
    public function __construct(?Escaper $escaper = null)
    {
        $this->escaper = $escaper ?? new Escaper('utf-8');
    }

    /**
     * Escape output for HTML content context
     *
     * Use this when outputting into HTML body/content
     *
     * Example:
     * ```php
     * echo '<div>' . $escaper->html($userName) . '</div>';
     * ```
     *
     * @param string|null $value Value to escape
     * @return string Escaped value safe for HTML content
     */
    public function html(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return $this->escaper->escapeHtml($value);
    }

    /**
     * Escape output for HTML attribute context
     *
     * Use this when outputting into HTML attributes
     *
     * Example:
     * ```php
     * echo '<input value="' . $escaper->attr($userInput) . '">';
     * echo '<div title="' . $escaper->attr($title) . '">';
     * ```
     *
     * @param string|null $value Value to escape
     * @return string Escaped value safe for HTML attributes
     */
    public function attr(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return $this->escaper->escapeHtmlAttr($value);
    }

    /**
     * Escape output for JavaScript context
     *
     * Use this when outputting into JavaScript strings
     *
     * Example:
     * ```php
     * echo '<script>var userName = "' . $escaper->js($userName) . '";</script>';
     * echo '<button onclick="showUser(\'' . $escaper->js($userId) . '\')">Click</button>';
     * ```
     *
     * @param string|null $value Value to escape
     * @return string Escaped value safe for JavaScript strings
     */
    public function js(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return $this->escaper->escapeJs($value);
    }

    /**
     * Escape output for URL/URI context
     *
     * Use this when outputting into href, src, or URL parameters
     *
     * Example:
     * ```php
     * echo '<a href="/user.php?id=' . $escaper->url($userId) . '">View</a>';
     * echo '<img src="' . $escaper->url($imagePath) . '">';
     * ```
     *
     * @param string|null $value Value to escape
     * @return string Escaped value safe for URLs
     */
    public function url(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return $this->escaper->escapeUrl($value);
    }

    /**
     * Escape output for CSS context
     *
     * Use this when outputting into CSS
     *
     * Example:
     * ```php
     * echo '<div style="color: ' . $escaper->css($userColor) . '">Text</div>';
     * ```
     *
     * @param string|null $value Value to escape
     * @return string Escaped value safe for CSS
     */
    public function css(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        return $this->escaper->escapeCss($value);
    }

    /**
     * Escape value for safe output in JSON
     *
     * Properly encodes value as JSON with additional XSS protection
     *
     * Example:
     * ```php
     * echo '<script>var data = ' . $escaper->json($userData) . ';</script>';
     * ```
     *
     * @param mixed $value Value to JSON encode
     * @return string JSON-encoded value
     */
    public function json($value): string
    {
        // JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_HEX_QUOT provide additional XSS protection
        // by encoding <, >, &, ', and " to unicode escape sequences
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Get the underlying Laminas Escaper instance
     *
     * Use this if you need direct access to the escaper
     *
     * @return Escaper
     */
    public function getEscaper(): Escaper
    {
        return $this->escaper;
    }
}

/**
 * Global helper function for quick HTML escaping
 *
 * This is a convenience function for the most common use case.
 * For other contexts (JS, CSS, URL), use the OutputEscaper class.
 *
 * Example:
 * ```php
 * echo '<div>' . esc_html($userName) . '</div>';
 * ```
 *
 * @param string|null $value Value to escape
 * @return string HTML-escaped value
 */
function esc_html(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Global helper function for quick HTML attribute escaping
 *
 * Example:
 * ```php
 * echo '<input value="' . esc_attr($userInput) . '">';
 * ```
 *
 * @param string|null $value Value to escape
 * @return string Attribute-escaped value
 */
function esc_attr(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Global helper function for quick JavaScript escaping
 *
 * Example:
 * ```php
 * echo '<button onclick="show(\'' . esc_js($value) . '\')">Click</button>';
 * ```
 *
 * @param string|null $value Value to escape
 * @return string JavaScript-escaped value
 */
function esc_js(?string $value): string
{
    if ($value === null) {
        return '';
    }

    // Escape special JavaScript characters
    $escaped = str_replace(
        ['\\', "'", '"', "\n", "\r", "\t", '<', '>', '&'],
        ['\\\\', "\\'", '\\"', '\\n', '\\r', '\\t', '\\x3C', '\\x3E', '\\x26'],
        $value
    );

    return $escaped;
}

/**
 * Global helper function for quick URL escaping
 *
 * Example:
 * ```php
 * echo '<a href="/page.php?id=' . esc_url($id) . '">Link</a>';
 * ```
 *
 * @param string|null $value Value to escape
 * @return string URL-escaped value
 */
function esc_url(?string $value): string
{
    if ($value === null) {
        return '';
    }
    return rawurlencode($value);
}
