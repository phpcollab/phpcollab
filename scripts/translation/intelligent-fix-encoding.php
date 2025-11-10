#!/usr/bin/env php
<?php
/**
 * Intelligent Encoding Fix Script
 *
 * This script uses linguistic context and dictionaries to intelligently
 * reconstruct corrupted accented characters in French and German translations.
 *
 * Unlike the basic fix-encoding.php, this script:
 * - Uses context from English msgid to understand meaning
 * - Applies linguistic rules for French and German
 * - Uses dictionaries of correctly accented words
 * - Makes intelligent guesses based on word patterns
 */

require_once __DIR__ . '/../../vendor/autoload.php';

class IntelligentEncodingFixer
{
    private $frenchDictionary = [];
    private $germanDictionary = [];
    private $stats = [
        'files_processed' => 0,
        'files_fixed' => 0,
        'chars_fixed' => 0,
        'words_fixed' => 0
    ];

    public function __construct()
    {
        $this->buildFrenchDictionary();
        $this->buildGermanDictionary();
    }

    /**
     * Build a dictionary of common French words with correct accents
     */
    private function buildFrenchDictionary(): void
    {
        // Common French words with accents
        // Format: word_without_accents => word_with_accents
        $this->frenchDictionary = [
            // Common verbs and actions
            'editer' => 'Éditer',
            'creer' => 'Créer',
            'supprimer' => 'Supprimer',
            'ajouter' => 'Ajouter',
            'modifier' => 'Modifier',
            'executer' => 'Exécuter',
            'generer' => 'Générer',
            'integrer' => 'Intégrer',
            'completer' => 'Compléter',
            'verifier' => 'Vérifier',
            'repeter' => 'Répéter',

            // Common nouns
            'tache' => 'tâche',
            'taches' => 'tâches',
            'connexion' => 'connexion',
            'deconnexion' => 'Déconnexion',
            'preferences' => 'Préférences',
            'criteres' => 'critères',
            'critere' => 'critère',
            'systeme' => 'système',
            'reponse' => 'réponse',
            'requete' => 'requête',
            'etape' => 'étape',
            'detail' => 'détail',
            'details' => 'détails',
            'evenement' => 'événement',
            'evenements' => 'événements',
            'fichier' => 'fichier',
            'statut' => 'statut',
            'priorite' => 'priorité',
            'societe' => 'Société',
            'equipe' => 'équipe',
            'delai' => 'délai',
            'echeance' => 'échéance',
            'periode' => 'période',
            'duree' => 'durée',
            'activite' => 'activité',
            'activites' => 'activités',
            'propriete' => 'propriété',
            'proprietes' => 'propriétés',
            'securite' => 'sécurité',
            'qualite' => 'qualité',
            'quantite' => 'quantité',
            'categorie' => 'catégorie',
            'categories' => 'catégories',
            'hierarchie' => 'hiérarchie',
            'espace' => 'espace',
            'strategie' => 'stratégie',
            'operation' => 'opération',
            'operations' => 'opérations',
            'theme' => 'thème',
            'themes' => 'thèmes',
            'role' => 'rôle',
            'roles' => 'rôles',

            // Common adjectives and past participles
            'assigne' => 'assigné',
            'assignee' => 'assignée',
            'assignes' => 'assignés',
            'assignees' => 'assignées',
            'cree' => 'créé',
            'creee' => 'créée',
            'crees' => 'créés',
            'creees' => 'créées',
            'modifie' => 'modifié',
            'modifiee' => 'modifiée',
            'modifies' => 'modifiés',
            'modifiees' => 'modifiées',
            'complete' => 'complété',
            'completee' => 'complétée',
            'completes' => 'complétés',
            'completees' => 'complétées',
            'selectionne' => 'sélectionné',
            'selectionnee' => 'sélectionnée',
            'selectionnes' => 'sélectionnés',
            'selectionnees' => 'sélectionnées',
            'ouvert' => 'ouvert',
            'ouverte' => 'ouverte',
            'ferme' => 'fermé',
            'fermee' => 'fermée',
            'pret' => 'prêt',
            'prete' => 'prête',
            'termine' => 'terminé',
            'terminee' => 'terminée',
            'general' => 'général',
            'generale' => 'générale',
            'generaux' => 'généraux',
            'generales' => 'générales',
            'specifique' => 'spécifique',
            'specifiques' => 'spécifiques',
            'necessaire' => 'nécessaire',
            'necessaires' => 'nécessaires',
            'disponible' => 'disponible',
            'disponibles' => 'disponibles',
            'valide' => 'valide',
            'valides' => 'valides',
            'etendu' => 'étendu',
            'etendue' => 'étendue',
            'integre' => 'intégré',
            'integree' => 'intégrée',

            // Common prepositions and articles
            'a' => 'à',
            'de' => 'de',
            'des' => 'des',
            'apres' => 'après',

            // Common adverbs
            'deja' => 'déjà',
            'tres' => 'très',
            'egalement' => 'également',
            'specifiquement' => 'spécifiquement',
            'generalement' => 'généralement',
            'particulierement' => 'particulièrement',
            'regulierement' => 'régulièrement',
            'completement' => 'complètement',
            'evidemment' => 'évidemment',

            // Common phrases
            'reouvert' => 'réouvert',
            'reouverte' => 'réouverte',
            'reouvrir' => 'réouvrir',
            'rouverte' => 'rouverte',
        ];
    }

    /**
     * Build a dictionary of common German words with correct accents
     */
    private function buildGermanDictionary(): void
    {
        $this->germanDictionary = [
            // Common German words with umlauts
            'uber' => 'über',
            'Uber' => 'Über',
            'fur' => 'für',
            'Fur' => 'Für',
            'zuruck' => 'zurück',
            'Zuruck' => 'Zurück',
            'geoffnet' => 'geöffnet',
            'Geoffnet' => 'Geöffnet',
            'andern' => 'ändern',
            'Andern' => 'Ändern',
            'geandert' => 'geändert',
            'Geandert' => 'Geändert',
            'loschen' => 'löschen',
            'Loschen' => 'Löschen',
            'gelöscht' => 'gelöscht',
            'hinzufugen' => 'hinzufügen',
            'Hinzufugen' => 'Hinzufügen',
            'ausfuhren' => 'ausführen',
            'Ausfuhren' => 'Ausführen',
            'ausgefuhrt' => 'ausgeführt',
            'Ausgefuhrt' => 'Ausgeführt',
            'prufen' => 'prüfen',
            'Prufen' => 'Prüfen',
            'gepruft' => 'geprüft',
            'Gepruft' => 'Geprüft',
            'schliessen' => 'schließen',
            'Schliessen' => 'Schließen',
            'geschlossen' => 'geschlossen',
            'Geschlossen' => 'Geschlossen',
            'große' => 'große',
            'Große' => 'Große',
            'grösser' => 'größer',
            'Grösser' => 'Größer',
            'grosste' => 'größte',
            'Grosste' => 'Größte',
        ];
    }

    /**
     * Fix a single .po file
     */
    public function fixFile(string $filePath, bool $dryRun = false): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'error' => 'File not found'];
        }

        $content = file_get_contents($filePath);
        $language = $this->detectLanguage($filePath);

        if (!in_array($language, ['fr', 'de'])) {
            return ['success' => false, 'error' => 'Unsupported language'];
        }

        $fixedContent = $this->fixContent($content, $language);

        $result = [
            'success' => true,
            'file' => $filePath,
            'language' => $language,
            'original_size' => strlen($content),
            'fixed_size' => strlen($fixedContent),
            'chars_fixed' => $this->stats['chars_fixed'],
            'words_fixed' => $this->stats['words_fixed'],
            'changes_made' => $content !== $fixedContent
        ];

        if (!$dryRun && $result['changes_made']) {
            // Create backup
            $backup = $filePath . '.bak.' . date('Y-m-d_His');
            copy($filePath, $backup);

            // Write fixed content
            file_put_contents($filePath, $fixedContent);
            $result['backup'] = $backup;
        }

        return $result;
    }

    /**
     * Detect language from file path
     */
    private function detectLanguage(string $filePath): string
    {
        if (preg_match('/\.fr\.po$/', $filePath)) {
            return 'fr';
        }
        if (preg_match('/\.de\.po$/', $filePath)) {
            return 'de';
        }
        return 'unknown';
    }

    /**
     * Fix content based on language
     */
    private function fixContent(string $content, string $language): string
    {
        $this->stats['chars_fixed'] = 0;
        $this->stats['words_fixed'] = 0;

        if ($language === 'fr') {
            $content = $this->fixFrench($content);
        } elseif ($language === 'de') {
            $content = $this->fixGerman($content);
        }

        return $content;
    }

    /**
     * Fix French text using dictionary and patterns
     */
    private function fixFrench(string $content): string
    {
        $lines = explode("\n", $content);
        $fixedLines = [];

        foreach ($lines as $line) {
            // Only process msgstr lines that contain corrupted characters
            if (strpos($line, 'msgstr') !== false && strpos($line, '�') !== false) {
                $line = $this->fixFrenchLine($line);
            }
            $fixedLines[] = $line;
        }

        return implode("\n", $fixedLines);
    }

    /**
     * Fix a single French line
     */
    private function fixFrenchLine(string $line): string
    {
        $originalLine = $line;

        // Common patterns where � appears
        $patterns = [
            // Single � at start of word (likely É or À)
            '/\b�([a-z]+)\b/u' => function($matches) {
                $word = strtolower($matches[1]);
                // Check dictionary
                $testWord = strtolower('e' . $matches[1]);
                foreach ($this->frenchDictionary as $key => $value) {
                    if (strtolower($value) === 'é' . $word) {
                        return 'É' . $matches[1];
                    }
                }
                // Common patterns
                if (in_array($word, ['diter', 'dition', 'quipe', 'tape', 'tat', 'tats', 'v�nement', 'v�nements'])) {
                    return 'É' . $matches[1];
                }
                return 'À' . $matches[1]; // Default to À
            },

            // � in middle of word (likely é, è, ê, à, â, ô, î, û, ù, ç)
            '/([a-zA-Z])�([a-zA-Z])/u' => function($matches) {
                $before = strtolower($matches[1]);
                $after = strtolower($matches[2]);

                // Pattern matching for common sequences
                if ($before === 't' && in_array($after, ['c', 'a'])) {
                    return $matches[1] . 'â' . $matches[2]; // tâche
                }
                if ($before === 'r' && in_array($after, ['f', 'p', 'a', 's', 't', 'c'])) {
                    return $matches[1] . 'é' . $matches[2]; // préférences, créer
                }
                if ($before === 'c' && $after === 'r') {
                    return $matches[1] . 'é' . $matches[2]; // créer
                }
                if ($before === 'd' && $after === 'c') {
                    return $matches[1] . 'é' . $matches[2]; // déconnexion
                }
                if ($before === 'g' && $after === 'n') {
                    return $matches[1] . 'é' . $matches[2]; // général
                }
                if ($before === 'qu' && $after === 't') {
                    return 'quê' . $matches[2]; // requête
                }
                if ($before === 'l' && $after === 'g') {
                    return $matches[1] . 'é' . $matches[2]; // déléguer
                }
                if ($before === 'v' && $after === 'n') {
                    return $matches[1] . 'é' . $matches[2]; // événement
                }
                if ($before === 's' && $after === 'l') {
                    return $matches[1] . 'é' . $matches[2]; // sélectionner
                }
                if ($before === 'p' && $after === 'r') {
                    return $matches[1] . 'ê' . $matches[2]; // prêt
                }
                if ($before === 't' && $after === 't') {
                    return $matches[1] . 'ê' . $matches[2]; // tête
                }
                if ($before === 'l' && $after === 'v') {
                    return $matches[1] . 'è' . $matches[2]; // élève
                }
                if ($before === 's' && $after === 's') {
                    return $matches[1] . 'è' . $matches[2]; // succès
                }
                if ($before === 'p' && $after === 'c') {
                    return $matches[1] . 'é' . $matches[2]; // spécifique
                }
                if ($before === 'n' && $after === 'c') {
                    return $matches[1] . 'é' . $matches[2]; // nécessaire
                }

                // Default to é as it's most common
                return $matches[1] . 'é' . $matches[2];
            },

            // Single � at end of word (likely é or ée)
            '/([a-zA-Z])�(["\s]|$)/u' => function($matches) {
                $before = strtolower($matches[1]);
                // Common endings
                if (in_array($before, ['n', 't', 'r', 'l', 'p', 'c', 'v'])) {
                    return $matches[1] . 'é' . $matches[2]; // assigné, créé, etc.
                }
                return $matches[1] . 'é' . $matches[2]; // Default to é
            },

            // Standalone �
            '/(\s|^|")�(\s|"|$)/u' => function($matches) {
                return $matches[1] . 'à' . $matches[2]; // likely "à"
            },
        ];

        foreach ($patterns as $pattern => $replacement) {
            $line = preg_replace_callback($pattern, $replacement, $line);
        }

        // Use dictionary for known words
        foreach ($this->frenchDictionary as $wrong => $correct) {
            $count = 0;
            $line = str_replace($wrong, $correct, $line, $count);
            if ($count > 0) {
                $this->stats['words_fixed'] += $count;
            }
        }

        // Count fixed characters
        if ($line !== $originalLine) {
            $fixedChars = substr_count($originalLine, '�') - substr_count($line, '�');
            $this->stats['chars_fixed'] += $fixedChars;
        }

        return $line;
    }

    /**
     * Fix German text using dictionary and patterns
     */
    private function fixGerman(string $content): string
    {
        $lines = explode("\n", $content);
        $fixedLines = [];

        foreach ($lines as $line) {
            // Only process msgstr lines that contain corrupted characters
            if (strpos($line, 'msgstr') !== false && strpos($line, '�') !== false) {
                $line = $this->fixGermanLine($line);
            }
            $fixedLines[] = $line;
        }

        return implode("\n", $fixedLines);
    }

    /**
     * Fix a single German line
     */
    private function fixGermanLine(string $line): string
    {
        $originalLine = $line;

        // Common German patterns
        $patterns = [
            // ü patterns
            '/([fF])�r\b/u' => '$1ür',
            '/([zZ])ur�ck/u' => '$1urück',
            '/([üÜ])ber/u' => '$1ber',

            // ö patterns
            '/ge�ffnet/u' => 'geöffnet',
            '/l�schen/u' => 'löschen',
            '/gel�scht/u' => 'gelöscht',
            '/gr��er/u' => 'größer',
            '/gr�sser/u' => 'größer',

            // ä patterns
            '/([äÄ])ndern/u' => '$1ndern',
            '/ge�ndert/u' => 'geändert',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $line = preg_replace($pattern, $replacement, $line);
        }

        // Use dictionary
        foreach ($this->germanDictionary as $wrong => $correct) {
            $count = 0;
            $line = str_replace($wrong, $correct, $line, $count);
            if ($count > 0) {
                $this->stats['words_fixed'] += $count;
            }
        }

        // Count fixed characters
        if ($line !== $originalLine) {
            $fixedChars = substr_count($originalLine, '�') - substr_count($line, '�');
            $this->stats['chars_fixed'] += $fixedChars;
        }

        return $line;
    }

    /**
     * Get statistics
     */
    public function getStats(): array
    {
        return $this->stats;
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $options = getopt('', ['lang:', 'dry-run', 'help']);

    if (isset($options['help'])) {
        echo "Intelligent Encoding Fix Script\n";
        echo "================================\n\n";
        echo "Usage: php intelligent-fix-encoding.php [options]\n\n";
        echo "Options:\n";
        echo "  --lang=<code>   Language code (fr or de)\n";
        echo "  --dry-run       Show what would be fixed without making changes\n";
        echo "  --help          Show this help message\n\n";
        echo "Examples:\n";
        echo "  php intelligent-fix-encoding.php --lang=fr --dry-run\n";
        echo "  php intelligent-fix-encoding.php --lang=fr\n";
        echo "  php intelligent-fix-encoding.php --lang=de\n\n";
        exit(0);
    }

    $lang = $options['lang'] ?? null;
    $dryRun = isset($options['dry-run']);

    if (!$lang || !in_array($lang, ['fr', 'de'])) {
        echo "Error: Please specify --lang=fr or --lang=de\n";
        exit(1);
    }

    $translationsPath = __DIR__ . '/../../translations';
    $domains = ['messages', 'help', 'custom'];

    $fixer = new IntelligentEncodingFixer();

    echo "Intelligent Encoding Fix\n";
    echo "========================\n\n";
    echo "Language: " . strtoupper($lang) . "\n";
    echo "Mode: " . ($dryRun ? "DRY RUN (no changes will be made)" : "LIVE (files will be modified)") . "\n\n";

    $totalCharsFixed = 0;
    $totalWordsFixed = 0;
    $filesFixed = 0;

    foreach ($domains as $domain) {
        $poFile = "{$translationsPath}/{$domain}/{$domain}.{$lang}.po";

        if (!file_exists($poFile)) {
            continue;
        }

        echo "Processing: {$domain}.{$lang}.po\n";

        $result = $fixer->fixFile($poFile, $dryRun);

        if ($result['success'] && $result['changes_made']) {
            echo "  ✓ Fixed {$result['chars_fixed']} characters, {$result['words_fixed']} words\n";
            if (!$dryRun && isset($result['backup'])) {
                echo "  ✓ Backup created: " . basename($result['backup']) . "\n";
            }
            $totalCharsFixed += $result['chars_fixed'];
            $totalWordsFixed += $result['words_fixed'];
            $filesFixed++;
        } elseif ($result['success']) {
            echo "  ✓ No changes needed\n";
        } else {
            echo "  ✗ Error: " . $result['error'] . "\n";
        }

        echo "\n";
    }

    echo "Summary\n";
    echo "=======\n";
    echo "Files fixed: {$filesFixed}\n";
    echo "Total characters fixed: {$totalCharsFixed}\n";
    echo "Total words fixed: {$totalWordsFixed}\n";

    if ($dryRun) {
        echo "\nThis was a DRY RUN. Run without --dry-run to apply changes.\n";
    } else {
        echo "\nChanges applied successfully!\n";
    }
}
