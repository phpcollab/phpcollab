<?php
/**
 * Translation Helper Functions
 *
 * Provides convenient functions for accessing translations throughout the application.
 * Uses Symfony Translation component to load .po files.
 */

use Symfony\Component\Translation\Translator;

/**
 * Get the translator instance from container
 *
 * @return Translator
 */
function getTranslator(): Translator
{
    global $container;

    if (!$container) {
        throw new RuntimeException('Container not initialized');
    }

    return $container->getTranslator();
}

/**
 * Translate a message
 *
 * @param string $key Translation key (e.g., 'strings.please_login' or 'please_login')
 * @param array $parameters Replacement parameters (e.g., ['%name%' => 'John'])
 * @param string $domain Translation domain (messages, help, enums, custom)
 * @param string|null $locale Override locale (default: current locale)
 * @return string Translated message
 */
function trans(string $key, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
{
    try {
        $translator = getTranslator();

        // Try with 'strings.' prefix if not already present and no translation found
        $result = $translator->trans($key, $parameters, $domain, $locale);

        // If key is returned unchanged, try with strings. prefix
        if ($result === $key && strpos($key, 'strings.') !== 0 && $domain === 'messages') {
            $result = $translator->trans('strings.' . $key, $parameters, $domain, $locale);
        }

        return $result;
    } catch (Exception $e) {
        // Fallback to key if translation fails
        error_log("Translation error for key '{$key}': " . $e->getMessage());
        return $key;
    }
}

/**
 * Translate with pluralization
 *
 * @param string $key Translation key
 * @param int $count Count for plural selection
 * @param array $parameters Replacement parameters
 * @param string $domain Translation domain
 * @param string|null $locale Override locale
 * @return string Translated message
 */
function transChoice(string $key, int $count, array $parameters = [], string $domain = 'messages', ?string $locale = null): string
{
    $parameters['%count%'] = $count;
    return trans($key, $parameters, $domain, $locale);
}

/**
 * Get enum value (status, priority, profil, etc.)
 *
 * @param string $enumType Enum type (status, priority, profil, etc.)
 * @param int|string $value Enum numeric value
 * @param string|null $locale Override locale
 * @return string Translated enum value
 */
function getEnum(string $enumType, $value, ?string $locale = null): string
{
    return trans("{$enumType}.{$value}", [], 'messages', $locale);
}

/**
 * Get all values for an enum as array
 *
 * @param string $enumType Enum type (status, priority, profil, etc.)
 * @param array $keys Array of numeric keys
 * @param string|null $locale Override locale
 * @return array Associative array [key => translated value]
 */
function getEnumArray(string $enumType, array $keys, ?string $locale = null): array
{
    $result = [];
    foreach ($keys as $key) {
        $result[$key] = getEnum($enumType, $key, $locale);
    }
    return $result;
}

/**
 * Get help text
 *
 * @param string $key Help key
 * @param array $parameters Replacement parameters
 * @param string|null $locale Override locale
 * @return string Help text
 */
function help(string $key, array $parameters = [], ?string $locale = null): string
{
    return trans($key, $parameters, 'help', $locale);
}

/**
 * Get custom translation
 *
 * @param string $key Custom key
 * @param array $parameters Replacement parameters
 * @param string|null $locale Override locale
 * @return string Custom translation
 */
function custom(string $key, array $parameters = [], ?string $locale = null): string
{
    return trans($key, $parameters, 'custom', $locale);
}

/**
 * Backward compatibility: Create $strings array from translations
 * DEPRECATED - Use trans() instead
 *
 * This is a temporary bridge for gradual migration.
 * Will be removed in future version.
 *
 * @return array
 */
function getLegacyStringsArray(): array
{
    static $strings = null;

    if ($strings !== null) {
        return $strings;
    }

    $strings = [];

    try {
        $translator = getTranslator();
        $catalogue = $translator->getCatalogue($translator->getLocale());

        // Get all messages from the 'messages' domain
        $messages = $catalogue->all('messages');

        // Remove 'strings.' prefix from keys for backward compatibility
        foreach ($messages as $key => $value) {
            if (strpos($key, 'strings.') === 0) {
                $shortKey = substr($key, 8); // Remove 'strings.' prefix
                $strings[$shortKey] = $value;
            } else {
                $strings[$key] = $value;
            }
        }
    } catch (Exception $e) {
        error_log("Error loading legacy strings array: " . $e->getMessage());
    }

    return $strings;
}

/**
 * Get day name
 *
 * @param int $dayNumber Day number (1-7, Monday=1, Sunday=7)
 * @param string|null $locale Override locale
 * @return string Day name
 */
function getDayName(int $dayNumber, ?string $locale = null): string
{
    return trans("dayname.{$dayNumber}", [], 'messages', $locale);
}

/**
 * Get month name
 *
 * @param int $monthNumber Month number (1-12)
 * @param string|null $locale Override locale
 * @return string Month name
 */
function getMonthName(int $monthNumber, ?string $locale = null): string
{
    return trans("monthname.{$monthNumber}", [], 'messages', $locale);
}

/**
 * Get byte unit
 *
 * @param int $index Index (0=Bytes, 1=KB, 2=MB, 3=GB)
 * @param string|null $locale Override locale
 * @return string Byte unit
 */
function getByteUnit(int $index, ?string $locale = null): string
{
    return trans("byteunits.{$index}", [], 'messages', $locale);
}
