#!/usr/bin/env php
<?php
/**
 * Context-Aware Encoding Fix Script
 *
 * Uses the English msgid to provide context for fixing corrupted French/German text.
 * This is much more accurate than pattern-based fixes alone.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

class ContextAwareEncodingFixer
{
    private $englishToFrench = [];
    private $englishToGerman = [];
    private $stats = ['chars_fixed' => 0, 'entries_fixed' => 0];

    public function __construct()
    {
        $this->buildEnglishToFrenchMap();
        $this->buildEnglishToGermanMap();
    }

    private function buildEnglishToFrenchMap(): void
    {
        // Mapping of English key parts to correct French translations
        // This provides context for fixing corrupted text
        $this->englishToFrench = [
            // Common action verbs
            'edit' => 'Éditer',
            'create' => 'Créer',
            'delete' => 'Supprimer',
            'add' => 'Ajouter',
            'modify' => 'Modifier',
            'update' => 'Mettre à jour',
            'search' => 'Rechercher',
            'filter' => 'Filtrer',
            'export' => 'Exporter',
            'import' => 'Importer',
            'publish' => 'Publier',
            'complete' => 'Compléter',
            'assign' => 'Assigner',
            'remove' => 'Retirer',
            'copy' => 'Copier',
            'select' => 'Sélectionner',
            'choose' => 'Choisir',
            'cancel' => 'Annuler',
            'submit' => 'Soumettre',
            'verify' => 'Vérifier',
            'validate' => 'Valider',
            'generate' => 'Générer',
            'calculate' => 'Calculer',
            'execute' => 'Exécuter',
            'integrate' => 'Intégrer',
            'configure' => 'Configurer',
            'archive' => 'Archiver',
            'restore' => 'Restaurer',
            'backup' => 'Sauvegarder',
            'download' => 'Télécharger',
            'upload' => 'Téléverser',

            // Common nouns
            'task' => 'tâche',
            'tasks' => 'tâches',
            'project' => 'projet',
            'projects' => 'projets',
            'user' => 'utilisateur',
            'users' => 'utilisateurs',
            'team' => 'équipe',
            'teams' => 'équipes',
            'client' => 'client',
            'clients' => 'clients',
            'company' => 'société',
            'organization' => 'organisation',
            'file' => 'fichier',
            'files' => 'fichiers',
            'document' => 'document',
            'documents' => 'documents',
            'report' => 'rapport',
            'reports' => 'rapports',
            'message' => 'message',
            'messages' => 'messages',
            'discussion' => 'discussion',
            'discussions' => 'discussions',
            'topic' => 'sujet',
            'topics' => 'sujets',
            'post' => 'message',
            'posts' => 'messages',
            'comment' => 'commentaire',
            'comments' => 'commentaires',
            'note' => 'note',
            'notes' => 'notes',
            'event' => 'événement',
            'events' => 'événements',
            'calendar' => 'calendrier',
            'date' => 'date',
            'dates' => 'dates',
            'time' => 'heure',
            'deadline' => 'échéance',
            'due' => 'échéance',
            'priority' => 'priorité',
            'priorities' => 'priorités',
            'status' => 'statut',
            'statuses' => 'statuts',
            'category' => 'catégorie',
            'categories' => 'catégories',
            'detail' => 'détail',
            'details' => 'détails',
            'description' => 'description',
            'summary' => 'résumé',
            'overview' => 'aperçu',
            'settings' => 'paramètres',
            'preferences' => 'préférences',
            'options' => 'options',
            'configuration' => 'configuration',
            'system' => 'système',
            'property' => 'propriété',
            'properties' => 'propriétés',
            'attribute' => 'attribut',
            'attributes' => 'attributs',
            'value' => 'valeur',
            'values' => 'valeurs',
            'criteria' => 'critères',
            'criterion' => 'critère',
            'query' => 'requête',
            'request' => 'requête',
            'response' => 'réponse',
            'result' => 'résultat',
            'results' => 'résultats',
            'success' => 'succès',
            'error' => 'erreur',
            'errors' => 'erreurs',
            'warning' => 'avertissement',
            'information' => 'information',
            'help' => 'aide',
            'support' => 'support',
            'faq' => 'FAQ',
            'search' => 'recherche',
            'filter' => 'filtre',
            'sort' => 'tri',
            'order' => 'ordre',
            'page' => 'page',
            'pages' => 'pages',
            'section' => 'section',
            'sections' => 'sections',
            'item' => 'élément',
            'items' => 'éléments',
            'list' => 'liste',
            'lists' => 'listes',
            'history' => 'historique',
            'log' => 'journal',
            'logs' => 'journaux',
            'notification' => 'notification',
            'notifications' => 'notifications',
            'alert' => 'alerte',
            'alerts' => 'alertes',
            'activity' => 'activité',
            'activities' => 'activités',
            'progress' => 'progrès',
            'duration' => 'durée',
            'period' => 'période',
            'start' => 'début',
            'end' => 'fin',
            'beginning' => 'début',
            'completion' => 'achèvement',
            'percent' => 'pourcent',
            'percentage' => 'pourcentage',
            'total' => 'total',
            'subtotal' => 'sous-total',
            'amount' => 'montant',
            'quantity' => 'quantité',
            'number' => 'numéro',
            'count' => 'nombre',
            'size' => 'taille',
            'type' => 'type',
            'types' => 'types',
            'format' => 'format',
            'formats' => 'formats',
            'version' => 'version',
            'versions' => 'versions',
            'update' => 'mise à jour',
            'updates' => 'mises à jour',
            'change' => 'changement',
            'changes' => 'changements',
            'security' => 'sécurité',
            'privacy' => 'confidentialité',
            'quality' => 'qualité',
            'performance' => 'performance',
            'efficiency' => 'efficacité',
            'capacity' => 'capacité',
            'limit' => 'limite',
            'limits' => 'limites',
            'maximum' => 'maximum',
            'minimum' => 'minimum',
            'average' => 'moyenne',
            'threshold' => 'seuil',
            'range' => 'plage',

            // Common phrases
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'log_in' => 'Se connecter',
            'log_out' => 'Se déconnecter',
            'sign_in' => 'Se connecter',
            'sign_out' => 'Se déconnecter',
            'register' => 'S\'inscrire',
            'welcome' => 'Bienvenue',
            'goodbye' => 'Au revoir',
            'hello' => 'Bonjour',
            'yes' => 'Oui',
            'no' => 'Non',
            'ok' => 'OK',
            'cancel' => 'Annuler',
            'close' => 'Fermer',
            'open' => 'Ouvrir',
            'save' => 'Enregistrer',
            'submit' => 'Soumettre',
            'reset' => 'Réinitialiser',
            'clear' => 'Effacer',
            'refresh' => 'Actualiser',
            'reload' => 'Recharger',
            'back' => 'Retour',
            'next' => 'Suivant',
            'previous' => 'Précédent',
            'first' => 'Premier',
            'last' => 'Dernier',
            'new' => 'Nouveau',
            'old' => 'Ancien',
            'recent' => 'Récent',
            'latest' => 'Dernier',
            'current' => 'Actuel',
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'enabled' => 'Activé',
            'disabled' => 'Désactivé',
            'available' => 'Disponible',
            'unavailable' => 'Indisponible',
            'online' => 'En ligne',
            'offline' => 'Hors ligne',
            'public' => 'Public',
            'private' => 'Privé',
            'shared' => 'Partagé',
            'personal' => 'Personnel',
            'general' => 'Général',
            'specific' => 'Spécifique',
            'default' => 'Par défaut',
            'custom' => 'Personnalisé',
            'standard' => 'Standard',
            'advanced' => 'Avancé',
            'basic' => 'Basique',
            'simple' => 'Simple',
            'complex' => 'Complexe',
            'required' => 'Requis',
            'optional' => 'Optionnel',
            'mandatory' => 'Obligatoire',
            'recommended' => 'Recommandé',
            'suggested' => 'Suggéré',
            'automatic' => 'Automatique',
            'manual' => 'Manuel',
            'readonly' => 'Lecture seule',
            'writable' => 'Modifiable',
            'locked' => 'Verrouillé',
            'unlocked' => 'Déverrouillé',
            'visible' => 'Visible',
            'hidden' => 'Caché',
            'show' => 'Afficher',
            'hide' => 'Masquer',
            'expand' => 'Développer',
            'collapse' => 'Réduire',
            'maximize' => 'Maximiser',
            'minimize' => 'Minimiser',
            'print' => 'Imprimer',
            'email' => 'Email',
            'send' => 'Envoyer',
            'receive' => 'Recevoir',
            'reply' => 'Répondre',
            'forward' => 'Transférer',
            'attach' => 'Joindre',
            'attachment' => 'Pièce jointe',
            'attachments' => 'Pièces jointes',
            'subject' => 'Sujet',
            'body' => 'Corps',
            'content' => 'Contenu',
            'title' => 'Titre',
            'name' => 'Nom',
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'username' => 'Nom d\'utilisateur',
            'password' => 'Mot de passe',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'address' => 'Adresse',
            'city' => 'Ville',
            'country' => 'Pays',
            'language' => 'Langue',
            'timezone' => 'Fuseau horaire',
            'currency' => 'Devise',

            // Prepositions and particles
            'to' => 'à',
            'from' => 'de',
            'by' => 'par',
            'for' => 'pour',
            'with' => 'avec',
            'without' => 'sans',
            'in' => 'dans',
            'on' => 'sur',
            'at' => 'à',
            'of' => 'de',
            'about' => 'à propos de',
            'after' => 'après',
            'before' => 'avant',
            'between' => 'entre',
            'during' => 'pendant',
            'since' => 'depuis',
            'until' => 'jusqu\'à',
            'through' => 'à travers',
            'under' => 'sous',
            'over' => 'sur',
            'above' => 'au-dessus',
            'below' => 'en dessous',

            // Common adjectives
            'assigned' => 'assignée',
            'created' => 'créé',
            'modified' => 'modifié',
            'updated' => 'mis à jour',
            'deleted' => 'supprimé',
            'completed' => 'complété',
            'selected' => 'sélectionné',
            'opened' => 'ouvert',
            'closed' => 'fermé',
            'ready' => 'prêt',
            'finished' => 'terminé',
        ];
    }

    private function buildEnglishToGermanMap(): void
    {
        $this->englishToGerman = [
            // Common German translations
            'for' => 'für',
            'over' => 'über',
            'about' => 'über',
            'back' => 'zurück',
            'change' => 'ändern',
            'changed' => 'geändert',
            'delete' => 'löschen',
            'deleted' => 'gelöscht',
            'add' => 'hinzufügen',
            'execute' => 'ausführen',
            'executed' => 'ausgeführt',
            'check' => 'prüfen',
            'checked' => 'geprüft',
            'close' => 'schließen',
            'closed' => 'geschlossen',
            'open' => 'öffnen',
            'opened' => 'geöffnet',
            'large' => 'groß',
            'larger' => 'größer',
            'largest' => 'größte',
        ];
    }

    public function fixFile(string $filePath, bool $dryRun = false): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'error' => 'File not found'];
        }

        $language = $this->detectLanguage($filePath);
        if (!in_array($language, ['fr', 'de'])) {
            return ['success' => false, 'error' => 'Unsupported language'];
        }

        $entries = $this->parsePoFile($filePath);
        $fixedEntries = $this->fixEntries($entries, $language);

        $result = [
            'success' => true,
            'file' => $filePath,
            'language' => $language,
            'entries_fixed' => $this->stats['entries_fixed'],
            'chars_fixed' => $this->stats['chars_fixed'],
            'changes_made' => $this->stats['entries_fixed'] > 0
        ];

        if (!$dryRun && $result['changes_made']) {
            // Create backup
            $backup = $filePath . '.bak.' . date('Y-m-d_His');
            copy($filePath, $backup);

            // Write fixed content
            $this->writePoFile($filePath, $fixedEntries);
            $result['backup'] = $backup;
        }

        return $result;
    }

    private function detectLanguage(string $filePath): string
    {
        if (preg_match('/\.fr\.po$/', $filePath)) return 'fr';
        if (preg_match('/\.de\.po$/', $filePath)) return 'de';
        return 'unknown';
    }

    private function parsePoFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);

        $entries = [];
        $currentEntry = null;
        $header = [];
        $inHeader = true;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            // Header section
            if ($inHeader) {
                $header[] = $line;
                if ($trimmed === '' && count($header) > 5) {
                    $inHeader = false;
                }
                continue;
            }

            // New entry starting
            if (strpos($trimmed, 'msgid ') === 0) {
                if ($currentEntry !== null) {
                    $entries[] = $currentEntry;
                }
                $currentEntry = [
                    'msgid' => $this->extractString($trimmed),
                    'msgstr' => '',
                    'comments' => []
                ];
            }
            // msgstr line
            elseif (strpos($trimmed, 'msgstr ') === 0 && $currentEntry !== null) {
                $currentEntry['msgstr'] = $this->extractString($trimmed);
            }
            // Continuation line
            elseif (strpos($trimmed, '"') === 0 && $currentEntry !== null) {
                if (empty($currentEntry['msgstr'])) {
                    $currentEntry['msgid'] .= ' ' . $this->extractString($trimmed);
                } else {
                    $currentEntry['msgstr'] .= ' ' . $this->extractString($trimmed);
                }
            }
            // Comment or empty line
            else {
                if ($currentEntry !== null && !empty($trimmed)) {
                    $currentEntry['comments'][] = $line;
                } elseif (empty($trimmed) && $currentEntry !== null) {
                    $currentEntry['separator'] = $line;
                }
            }
        }

        if ($currentEntry !== null) {
            $entries[] = $currentEntry;
        }

        return ['header' => implode("\n", $header), 'entries' => $entries];
    }

    private function extractString(string $line): string
    {
        // Extract string from msgid "string" or msgstr "string" format
        if (preg_match('/"([^"]*)"/', $line, $matches)) {
            return $matches[1];
        }
        return '';
    }

    private function fixEntries(array $parsed, string $language): array
    {
        $this->stats = ['chars_fixed' => 0, 'entries_fixed' => 0];

        foreach ($parsed['entries'] as &$entry) {
            if (empty($entry['msgstr']) || strpos($entry['msgstr'], '�') === false) {
                continue;
            }

            $originalMsgstr = $entry['msgstr'];
            $fixedMsgstr = $this->fixWithContext($entry['msgid'], $entry['msgstr'], $language);

            if ($fixedMsgstr !== $originalMsgstr) {
                $entry['msgstr'] = $fixedMsgstr;
                $this->stats['entries_fixed']++;
                $charsFixed = substr_count($originalMsgstr, '�') - substr_count($fixedMsgstr, '�');
                $this->stats['chars_fixed'] += $charsFixed;
            }
        }

        return $parsed;
    }

    private function fixWithContext(string $msgid, string $msgstr, string $language): string
    {
        if ($language === 'fr') {
            return $this->fixFrenchWithContext($msgid, $msgstr);
        } elseif ($language === 'de') {
            return $this->fixGermanWithContext($msgid, $msgstr);
        }
        return $msgstr;
    }

    private function fixFrenchWithContext(string $msgid, string $msgstr): string
    {
        // Extract key words from msgid
        $msgidLower = strtolower($msgid);
        $msgidWords = preg_split('/[^a-z]+/', $msgidLower, -1, PREG_SPLIT_NO_EMPTY);

        // Check if we have a direct translation for any key word
        foreach ($msgidWords as $word) {
            if (isset($this->englishToFrench[$word])) {
                $correctFrench = $this->englishToFrench[$word];
                $correctLower = mb_strtolower($correctFrench, 'UTF-8');

                // Create a corrupted version pattern
                $corrupted = $this->createCorruptedPattern($correctLower);

                // Replace corrupted version with correct version
                $msgstr = preg_replace_callback(
                    '/' . preg_quote($corrupted, '/') . '/ui',
                    function() use ($correctFrench) {
                        return $correctFrench;
                    },
                    $msgstr
                );
            }
        }

        // Apply general French accent fixes for remaining � characters
        $msgstr = $this->applyGeneralFrenchFixes($msgstr);

        return $msgstr;
    }

    private function createCorruptedPattern(string $correct): string
    {
        // Replace accented characters with �
        $pattern = $correct;
        $pattern = preg_replace('/[éèêë]/u', '�', $pattern);
        $pattern = preg_replace('/[àâä]/u', '�', $pattern);
        $pattern = preg_replace('/[îï]/u', '�', $pattern);
        $pattern = preg_replace('/[ôö]/u', '�', $pattern);
        $pattern = preg_replace('/[ùûü]/u', '�', $pattern);
        $pattern = preg_replace('/[ç]/u', '�', $pattern);
        $pattern = preg_replace('/[ÉÈÊË]/u', '�', $pattern);
        $pattern = preg_replace('/[ÀÂÄ]/u', '�', $pattern);
        $pattern = preg_replace('/[ÎÏ]/u', '�', $pattern);
        $pattern = preg_replace('/[ÔÖ]/u', '�', $pattern);
        $pattern = preg_replace('/[ÙÛÜ]/u', '�', $pattern);
        $pattern = preg_replace('/[Ç]/u', '�', $pattern);
        return $pattern;
    }

    private function applyGeneralFrenchFixes(string $text): string
    {
        // Common French patterns that can be fixed without context
        $replacements = [
            '/\bt�che\b/ui' => 'tâche',
            '/\bt�ches\b/ui' => 'tâches',
            '/\bpr�f�rences\b/ui' => 'préférences',
            '/\bd�connexion\b/ui' => 'déconnexion',
            '/\bconnexion\b/ui' => 'connexion',
            '/\bassign�e\b/ui' => 'assignée',
            '/\bcr�er\b/ui' => 'créer',
            '/\bcr�e\b/ui' => 'créée',
            '/\bcr�es\b/ui' => 'créées',
            '/\bcr�é\b/ui' => 'créé',
            '/\bcr�és\b/ui' => 'créés',
            '/\b�diter\b/ui' => 'éditer',
            '/\b�dition\b/ui' => 'édition',
            '/\bd�tails\b/ui' => 'détails',
            '/\bd�tail\b/ui' => 'détail',
            '/\b�v�nement\b/ui' => 'événement',
            '/\b�v�nements\b/ui' => 'événements',
            '/\brequ�te\b/ui' => 'requête',
            '/\brequ�tes\b/ui' => 'requêtes',
            '/\bcrit�re\b/ui' => 'critère',
            '/\bcrit�res\b/ui' => 'critères',
            '/\bsyst�me\b/ui' => 'système',
            '/\br�ponse\b/ui' => 'réponse',
            '/\br�ponses\b/ui' => 'réponses',
            '/\b�tape\b/ui' => 'étape',
            '/\b�tapes\b/ui' => 'étapes',
            '/\bs�lectionn�e\b/ui' => 'sélectionnée',
            '/\bs�lectionn�es\b/ui' => 'sélectionnées',
            '/\bs�lectionn�\b/ui' => 'sélectionné',
            '/\bs�lectionn�s\b/ui' => 'sélectionnés',
            '/\bcompl�t�\b/ui' => 'complété',
            '/\bcompl�t�e\b/ui' => 'complétée',
            '/\bcompl�t�s\b/ui' => 'complétés',
            '/\bcompl�t�es\b/ui' => 'complétées',
            '/\bpriori�t�\b/ui' => 'priorité',
            '/\bpriori�t�s\b/ui' => 'priorités',
            '/\bsoci�t�\b/ui' => 'société',
            '/\b�quipe\b/ui' => 'équipe',
            '/\b�quipes\b/ui' => 'équipes',
            '/\bd�lai\b/ui' => 'délai',
            '/\bd�lais\b/ui' => 'délais',
            '/\b�ch�ance\b/ui' => 'échéance',
            '/\b�ch�ances\b/ui' => 'échéances',
            '/\bp�riode\b/ui' => 'période',
            '/\bp�riodes\b/ui' => 'périodes',
            '/\bdur�e\b/ui' => 'durée',
            '/\bdur�es\b/ui' => 'durées',
            '/\bactivit�\b/ui' => 'activité',
            '/\bactivit�s\b/ui' => 'activités',
            '/\bpropri�t�\b/ui' => 'propriété',
            '/\bpropri�t�s\b/ui' => 'propriétés',
            '/\bs�curit�\b/ui' => 'sécurité',
            '/\bqualit�\b/ui' => 'qualité',
            '/\bquantit�\b/ui' => 'quantité',
            '/\bcat�gorie\b/ui' => 'catégorie',
            '/\bcat�gories\b/ui' => 'catégories',
            '/\bhi�rarchie\b/ui' => 'hiérarchie',
            '/\bstrat�gie\b/ui' => 'stratégie',
            '/\bstrat�gies\b/ui' => 'stratégies',
            '/\bop�ration\b/ui' => 'opération',
            '/\bop�rations\b/ui' => 'opérations',
            '/\bth�me\b/ui' => 'thème',
            '/\bth�mes\b/ui' => 'thèmes',
            '/\br�le\b/ui' => 'rôle',
            '/\br�les\b/ui' => 'rôles',
            '/\bpr�t\b/ui' => 'prêt',
            '/\bpr�te\b/ui' => 'prête',
            '/\btermin�\b/ui' => 'terminé',
            '/\btermin�e\b/ui' => 'terminée',
            '/\bg�n�ral\b/ui' => 'général',
            '/\bg�n�rale\b/ui' => 'générale',
            '/\bg�n�raux\b/ui' => 'généraux',
            '/\bg�n�rales\b/ui' => 'générales',
            '/\bg�n�rer\b/ui' => 'générer',
            '/\bsp�cifique\b/ui' => 'spécifique',
            '/\bsp�cifiques\b/ui' => 'spécifiques',
            '/\bn�cessaire\b/ui' => 'nécessaire',
            '/\bn�cessaires\b/ui' => 'nécessaires',
            '/\bdisponible\b/ui' => 'disponible',
            '/\bdisponibles\b/ui' => 'disponibles',
            '/\bint�gr�\b/ui' => 'intégré',
            '/\bint�gr�e\b/ui' => 'intégrée',
            '/\bint�grer\b/ui' => 'intégrer',
            '/\bd�j�\b/ui' => 'déjà',
            '/\btr�s\b/ui' => 'très',
            '/\b�galement\b/ui' => 'également',
            '/\bapr�s\b/ui' => 'après',
            '/\br�ouvert\b/ui' => 'réouvert',
            '/\br�ouverte\b/ui' => 'réouverte',
            '/\br�ouvrir\b/ui' => 'réouvrir',
            '/\beffac�\b/ui' => 'effacé',
            '/\beffac�e\b/ui' => 'effacée',
            '/\bferm�\b/ui' => 'fermé',
            '/\bferm�e\b/ui' => 'fermée',
            '/\bd�faut\b/ui' => 'défaut',
            '/\bd�finir\b/ui' => 'définir',
            '/\bd�fini\b/ui' => 'défini',
            '/\bd�finie\b/ui' => 'définie',
            '/\bmodifi�\b/ui' => 'modifié',
            '/\bmodifi�e\b/ui' => 'modifiée',
            '/\bmodifi�s\b/ui' => 'modifiés',
            '/\bmodifi�es\b/ui' => 'modifiées',
            '/\bvalid�\b/ui' => 'validé',
            '/\bvalid�e\b/ui' => 'validée',
            '/\b�tendu\b/ui' => 'étendu',
            '/\b�tendue\b/ui' => 'étendue',
            '/\bpropri�taire\b/ui' => 'propriétaire',
            '/\b�l�ment\b/ui' => 'élément',
            '/\b�l�ments\b/ui' => 'éléments',
            '/\baperçu\b/ui' => 'aperçu',
            '/\baperçus\b/ui' => 'aperçus',
            '/\bresumer\b/ui' => 'résumer',
            '/\bresume\b/ui' => 'résumé',
            '/\bresumes\b/ui' => 'résumés',
            '/\bachev�\b/ui' => 'achevé',
            '/\bachev�e\b/ui' => 'achevée',
            '/\bexecut�\b/ui' => 'exécuté',
            '/\bexecut�e\b/ui' => 'exécutée',
            '/\bex�cut�\b/ui' => 'exécuté',
            '/\bex�cut�e\b/ui' => 'exécutée',
            '/\bex�cuter\b/ui' => 'exécuter',
            '/\bparam�tre\b/ui' => 'paramètre',
            '/\bparam�tres\b/ui' => 'paramètres',
            '/\bsucc�s\b/ui' => 'succès',
            '/\barch�v�\b/ui' => 'archivé',
            '/\barch�v�e\b/ui' => 'archivée',
            '/\bt�l�charger\b/ui' => 'télécharger',
            '/\bt�l�charg�\b/ui' => 'téléchargé',
            '/\bt�l�charg�e\b/ui' => 'téléchargée',
            '/\br�initialiser\b/ui' => 'réinitialiser',
            '/\br�initialis�\b/ui' => 'réinitialisé',
            '/\bpr�c�dent\b/ui' => 'précédent',
            '/\bpr�c�dente\b/ui' => 'précédente',
            '/\br�cent\b/ui' => 'récent',
            '/\br�cente\b/ui' => 'récente',
            '/\br�cents\b/ui' => 'récents',
            '/\br�centes\b/ui' => 'récentes',
            '/\bactiv�\b/ui' => 'activé',
            '/\bactiv�e\b/ui' => 'activée',
            '/\bd�sactiv�\b/ui' => 'désactivé',
            '/\bd�sactiv�e\b/ui' => 'désactivée',
            '/\bd�verrouill�\b/ui' => 'déverrouillé',
            '/\bd�verrouill�e\b/ui' => 'déverrouillée',
            '/\bd�velopper\b/ui' => 'développer',
            '/\bd�velopp�\b/ui' => 'développé',
            '/\bd�velopp�e\b/ui' => 'développée',
            '/\br�duire\b/ui' => 'réduire',
            '/\br�duit\b/ui' => 'réduit',
            '/\br�duite\b/ui' => 'réduite',
            '/\btransf�rer\b/ui' => 'transférer',
            '/\btransf�r�\b/ui' => 'transféré',
            '/\btransf�r�e\b/ui' => 'transférée',
            '/\bpi�ce\b/ui' => 'pièce',
            '/\bpi�ces\b/ui' => 'pièces',
            '/\bpr�nom\b/ui' => 'prénom',
            '/\bt�l�phone\b/ui' => 'téléphone',
            '/\b�\b/u' => 'à',  // Standalone �
            '/\b�tre\b/ui' => 'être',
            '/\b�tait\b/ui' => 'était',
            '/\b�taient\b/ui' => 'étaient',
            '/\b�t�\b/ui' => 'été',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

    private function fixGermanWithContext(string $msgid, string $msgstr): string
    {
        // Similar approach for German
        $msgidLower = strtolower($msgid);
        $msgidWords = preg_split('/[^a-z]+/', $msgidLower, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($msgidWords as $word) {
            if (isset($this->englishToGerman[$word])) {
                $correctGerman = $this->englishToGerman[$word];
                $corrupted = $this->createCorruptedPattern($correctGerman);

                $msgstr = preg_replace_callback(
                    '/' . preg_quote($corrupted, '/') . '/ui',
                    function() use ($correctGerman) {
                        return $correctGerman;
                    },
                    $msgstr
                );
            }
        }

        return $this->applyGeneralGermanFixes($msgstr);
    }

    private function applyGeneralGermanFixes(string $text): string
    {
        $replacements = [
            '/\bf�r\b/ui' => 'für',
            '/\b�ber\b/ui' => 'über',
            '/\bzur�ck\b/ui' => 'zurück',
            '/\bge�ffnet\b/ui' => 'geöffnet',
            '/\b�ndern\b/ui' => 'ändern',
            '/\bge�ndert\b/ui' => 'geändert',
            '/\bl�schen\b/ui' => 'löschen',
            '/\bgel�scht\b/ui' => 'gelöscht',
            '/\bhinzuf�gen\b/ui' => 'hinzufügen',
            '/\bausf�hren\b/ui' => 'ausführen',
            '/\bausgef�hrt\b/ui' => 'ausgeführt',
            '/\bpr�fen\b/ui' => 'prüfen',
            '/\bgepr�ft\b/ui' => 'geprüft',
            '/\bschlie�en\b/ui' => 'schließen',
            '/\bgeschlossen\b/ui' => 'geschlossen',
            '/\b�ffnen\b/ui' => 'öffnen',
            '/\bgro�\b/ui' => 'groß',
            '/\bgr��er\b/ui' => 'größer',
            '/\bgr��te\b/ui' => 'größte',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

    private function writePoFile(string $filePath, array $parsed): void
    {
        $output = $parsed['header'] . "\n\n";

        foreach ($parsed['entries'] as $entry) {
            // Write comments
            if (!empty($entry['comments'])) {
                $output .= implode("\n", $entry['comments']) . "\n";
            }

            // Write msgid
            $output .= 'msgid "' . $entry['msgid'] . '"' . "\n";

            // Write msgstr
            $output .= 'msgstr "' . $entry['msgstr'] . '"' . "\n";

            // Add separator
            $output .= "\n";
        }

        file_put_contents($filePath, $output);
    }
}

// CLI Interface
if (php_sapi_name() === 'cli') {
    $options = getopt('', ['lang:', 'dry-run', 'help']);

    if (isset($options['help'])) {
        echo "Context-Aware Encoding Fix Script\n";
        echo "==================================\n\n";
        echo "Usage: php context-aware-fix.php [options]\n\n";
        echo "Options:\n";
        echo "  --lang=<code>   Language code (fr or de)\n";
        echo "  --dry-run       Show what would be fixed without making changes\n";
        echo "  --help          Show this help message\n\n";
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

    $fixer = new ContextAwareEncodingFixer();

    echo "Context-Aware Encoding Fix\n";
    echo "===========================\n\n";
    echo "Language: " . strtoupper($lang) . "\n";
    echo "Mode: " . ($dryRun ? "DRY RUN" : "LIVE") . "\n\n";

    $totalCharsFixed = 0;
    $totalEntriesFixed = 0;

    foreach ($domains as $domain) {
        $poFile = "{$translationsPath}/{$domain}/{$domain}.{$lang}.po";

        if (!file_exists($poFile)) {
            continue;
        }

        echo "Processing: {$domain}.{$lang}.po\n";

        $result = $fixer->fixFile($poFile, $dryRun);

        if ($result['success'] && $result['changes_made']) {
            echo "  ✓ Fixed {$result['entries_fixed']} entries, {$result['chars_fixed']} characters\n";
            if (!$dryRun && isset($result['backup'])) {
                echo "  ✓ Backup: " . basename($result['backup']) . "\n";
            }
            $totalEntriesFixed += $result['entries_fixed'];
            $totalCharsFixed += $result['chars_fixed'];
        } else {
            echo "  ✓ No changes needed\n";
        }
        echo "\n";
    }

    echo "Summary\n";
    echo "=======\n";
    echo "Entries fixed: {$totalEntriesFixed}\n";
    echo "Characters fixed: {$totalCharsFixed}\n";

    if ($dryRun) {
        echo "\nThis was a DRY RUN. Run without --dry-run to apply changes.\n";
    }
}
