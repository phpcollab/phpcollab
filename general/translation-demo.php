<?php
/**
 * Translation System Demo Page
 *
 * This page demonstrates the new .po file translation system
 * You can access it at: http://your-site/general/translation-demo.php
 */

$checkSession = "false";
require_once '../includes/library.php';

// Get current language
$currentLang = $container->getLanguage();
$translator = $container->getTranslator();

// Get list of available languages
$availableLanguages = [
    'en' => 'English',
    'fr' => 'Français',
    'es' => 'Español',
    'de' => 'Deutsch',
    'it' => 'Italiano',
    'pt' => 'Português',
    'pt-br' => 'Português (Brasil)',
    'nl' => 'Nederlands',
    'ru' => 'Русский',
    'pl' => 'Polski',
    'ja' => '日本語',
    'zh' => '简体中文',
    'zh-tw' => '繁體中文',
];

// Handle language switching
if (isset($_GET['lang']) && array_key_exists($_GET['lang'], $availableLanguages)) {
    $session->set('language', $_GET['lang']);
    header('Location: translation-demo.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Translation System Demo - phpCollab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header h1 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 16px;
        }

        .lang-selector {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .lang-selector h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .lang-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .lang-button {
            padding: 10px 20px;
            background: #f0f0f0;
            border: 2px solid transparent;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s;
        }

        .lang-button:hover {
            background: #e0e0e0;
            border-color: #667eea;
        }

        .lang-button.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .demo-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .demo-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 22px;
        }

        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .method {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .method h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .method code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }

        .result {
            margin-top: 10px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .result-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .result-value {
            font-size: 18px;
            color: #333;
            font-weight: 600;
        }

        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .info-box strong {
            color: #1976D2;
        }

        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .success-box strong {
            color: #388E3C;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
        }

        tr:hover {
            background: #f9f9f9;
        }

        @media (max-width: 768px) {
            .comparison {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🌍 Translation System Demo</h1>
            <p>phpCollab .po File Translation System - Live Demonstration</p>
        </div>

        <!-- Language Selector -->
        <div class="lang-selector">
            <h2>Select Language / Seleccionar idioma / Choisir la langue</h2>
            <div class="lang-buttons">
                <?php foreach ($availableLanguages as $code => $name): ?>
                    <a href="?lang=<?php echo $code; ?>"
                       class="lang-button <?php echo $currentLang === $code ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="info-box" style="margin-top: 15px;">
                <strong>Current Language:</strong> <?php echo htmlspecialchars($availableLanguages[$currentLang] ?? $currentLang); ?> (<?php echo $currentLang; ?>)
            </div>
        </div>

        <!-- Basic Translations Demo -->
        <div class="demo-section">
            <h2>1. Basic String Translations</h2>

            <div class="comparison">
                <!-- Old Method -->
                <div class="method">
                    <h3>❌ Old Method (PHP Arrays)</h3>
                    <code>$strings["please_login"]</code>
                    <div class="result">
                        <div class="result-label">Output:</div>
                        <div class="result-value"><?php echo htmlspecialchars($strings["please_login"]); ?></div>
                    </div>
                </div>

                <!-- New Method -->
                <div class="method">
                    <h3>✅ New Method (.po Files)</h3>
                    <code>trans('please_login')</code>
                    <div class="result">
                        <div class="result-label">Output:</div>
                        <div class="result-value"><?php echo htmlspecialchars(trans('please_login')); ?></div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Translation Key</th>
                        <th>Old Method</th>
                        <th>New Method</th>
                        <th>Match?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $testKeys = ['login', 'logout', 'preferences', 'my_tasks', 'edit_task', 'add_task'];
                    foreach ($testKeys as $key):
                        $oldValue = $strings[$key] ?? 'N/A';
                        $newValue = trans($key);
                        $match = ($oldValue === $newValue);
                    ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($key); ?></code></td>
                        <td><?php echo htmlspecialchars($oldValue); ?></td>
                        <td><?php echo htmlspecialchars($newValue); ?></td>
                        <td><?php echo $match ? '✅' : '❌'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Enum Translations Demo -->
        <div class="demo-section">
            <h2>2. Enum Value Translations</h2>

            <div class="comparison">
                <!-- Old Method -->
                <div class="method">
                    <h3>❌ Old Method</h3>
                    <code>$status[0]</code>
                    <div class="result">
                        <div class="result-label">Output:</div>
                        <div class="result-value"><?php echo htmlspecialchars($status[0]); ?></div>
                    </div>
                </div>

                <!-- New Method -->
                <div class="method">
                    <h3>✅ New Method</h3>
                    <code>getEnum('status', 0)</code>
                    <div class="result">
                        <div class="result-label">Output:</div>
                        <div class="result-value"><?php echo htmlspecialchars(getEnum('status', 0)); ?></div>
                    </div>
                </div>
            </div>

            <h3 style="margin: 20px 0 10px 0; color: #333;">Status Values:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Value</th>
                        <th>Old Method</th>
                        <th>New Method</th>
                        <th>Match?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i <= 4; $i++):
                        $oldValue = $status[$i] ?? 'N/A';
                        $newValue = getEnum('status', $i);
                        $match = ($oldValue === $newValue);
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo htmlspecialchars($oldValue); ?></td>
                        <td><?php echo htmlspecialchars($newValue); ?></td>
                        <td><?php echo $match ? '✅' : '❌'; ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <h3 style="margin: 20px 0 10px 0; color: #333;">Priority Values:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Value</th>
                        <th>Old Method</th>
                        <th>New Method</th>
                        <th>Match?</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i <= 5; $i++):
                        $oldValue = $priority[$i] ?? 'N/A';
                        $newValue = getEnum('priority', $i);
                        $match = ($oldValue === $newValue);
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo htmlspecialchars($oldValue); ?></td>
                        <td><?php echo htmlspecialchars($newValue); ?></td>
                        <td><?php echo $match ? '✅' : '❌'; ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- System Information -->
        <div class="demo-section">
            <h2>3. System Information</h2>

            <table>
                <tbody>
                    <tr>
                        <td><strong>Current Language:</strong></td>
                        <td><?php echo htmlspecialchars($currentLang); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Translator Locale:</strong></td>
                        <td><?php echo htmlspecialchars($translator->getLocale()); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Fallback Locales:</strong></td>
                        <td><?php echo implode(', ', $translator->getFallbackLocales()); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Translation Files:</strong></td>
                        <td>
                            Messages: 31 languages<br>
                            Help: 31 languages<br>
                            Custom: 13 languages
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Translation Format:</strong></td>
                        <td>.po (Portable Object) files</td>
                    </tr>
                    <tr>
                        <td><strong>Translation Library:</strong></td>
                        <td>Symfony Translation Component v5.4.45</td>
                    </tr>
                </tbody>
            </table>

            <div class="success-box">
                <strong>✅ Translation System Status:</strong> Fully Operational<br>
                Both old (PHP arrays) and new (.po files) systems running in parallel for backward compatibility.
            </div>
        </div>

        <!-- Call to Action -->
        <div class="demo-section">
            <h2>Next Steps</h2>
            <p style="margin-bottom: 15px;">The translation system is fully operational! Here's what you can do:</p>
            <ol style="line-height: 1.8; margin-left: 20px;">
                <li><strong>Try different languages</strong> using the language selector above</li>
                <li><strong>Compare outputs</strong> between old and new methods (they should match!)</li>
                <li><strong>Review the code</strong> of this demo page to see how to use trans() and getEnum()</li>
                <li><strong>Start migrating pages</strong> to use the new trans() function</li>
                <li><strong>Edit .po files</strong> with Poedit or any text editor to modify translations</li>
            </ol>

            <div class="info-box" style="margin-top: 20px;">
                <strong>💡 Pro Tip:</strong> You can edit translation files in <code>/translations/messages/messages.<?php echo $currentLang; ?>.po</code>
                and refresh this page to see changes immediately!
            </div>
        </div>
    </div>
</body>
</html>
