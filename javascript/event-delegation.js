/**
 * PhpCollab - Event Delegation System
 * Phase 2 - Unobtrusive JavaScript
 *
 * This module handles all interactive elements using event delegation,
 * eliminating the need for inline event handlers (onclick, onmouseover, etc.)
 *
 * Benefits:
 * - Cleaner HTML with no inline JavaScript
 * - Better CSP compliance
 * - Easier to test and maintain
 * - Works with dynamically added content
 * - Better separation of concerns
 */

(function(window, document) {
    'use strict';

    /**
     * Event Delegation Manager
     */
    const EventDelegation = {

        /**
         * Initialize all event delegations
         */
        init: function() {
            this.setupToggleLinks();
            this.setupSortLinks();
            this.setupCheckboxSelectAll();
            this.setupCheckboxRows();
            this.setupPaletteButtons();
            this.setupTooltipLinks();
        },

        /**
         * Setup toggle/collapse functionality
         * Handles: <a class="toggle-link" data-toggle-target="..." data-theme="...">
         */
        setupToggleLinks: function() {
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.toggle-link');
                if (!link) return;

                e.preventDefault();

                const targetId = link.getAttribute('data-toggle-target');
                const theme = link.getAttribute('data-theme');

                if (targetId && theme && typeof window.showHideModule === 'function') {
                    window.showHideModule(targetId, theme);
                }
            });
        },

        /**
         * Setup sorting links
         * Handles: <a class="sort-link" data-form="..." data-sort-target="..." data-sort-field="..." data-sort-order="...">
         */
        setupSortLinks: function() {
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.sort-link');
                if (!link) return;

                e.preventDefault();

                const formName = link.getAttribute('data-form');
                const sortTarget = link.getAttribute('data-sort-target');
                const sortField = link.getAttribute('data-sort-field');
                const sortOrder = link.getAttribute('data-sort-order');

                const form = document.forms[formName + 'Form'];
                if (!form) return;

                // Set hidden field values
                if (form.elements['sort_target']) {
                    form.elements['sort_target'].value = sortTarget;
                }
                if (form.elements['sort_fields']) {
                    form.elements['sort_fields'].value = sortField;
                }
                if (form.elements['sort_order']) {
                    form.elements['sort_order'].value = sortOrder;
                }

                // Submit form
                form.submit();
            });
        },

        /**
         * Setup "Select All" checkbox functionality
         * Handles: <a class="checkbox-select-all" data-form="..." data-theme="...">
         */
        setupCheckboxSelectAll: function() {
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.checkbox-select-all');
                if (!link) return;

                e.preventDefault();

                const formName = link.getAttribute('data-form');
                const theme = link.getAttribute('data-theme');

                const form = document.forms[formName + 'Form'];
                if (!form) return;

                if (typeof window.MM_toggleSelectedItems === 'function') {
                    window.MM_toggleSelectedItems(form, theme);
                }
            });
        },

        /**
         * Setup individual checkbox row functionality
         * Handles: <a class="checkbox-toggle" data-form="..." data-item-id="..." data-image-id="..." data-theme="...">
         */
        setupCheckboxRows: function() {
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.checkbox-toggle');
                if (!link) return;

                e.preventDefault();

                const formName = link.getAttribute('data-form');
                const itemId = link.getAttribute('data-item-id');
                const imageId = link.getAttribute('data-image-id');
                const theme = link.getAttribute('data-theme');

                const form = document.forms[formName + 'Form'];
                if (!form) return;

                if (typeof window.MM_toggleItem === 'function') {
                    window.MM_toggleItem(form, itemId, imageId, theme);
                }
            });
        },

        /**
         * Setup palette button interactions
         * Handles: <a class="palette-button" data-form="..." data-button-name="...">
         */
        setupPaletteButtons: function() {
            // Click handler
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.palette-button');
                if (!link) return;

                e.preventDefault();

                const formName = link.getAttribute('data-form');
                const buttonName = link.getAttribute('data-button-name');

                const form = document.forms[formName + 'Form'];
                if (!form) return;

                if (typeof window.MM_getButtonWithName === 'function') {
                    const button = window.MM_getButtonWithName(form, buttonName);
                    if (button && button.click) {
                        button.click();
                    }
                }
            });

            // Mouseover handler
            document.addEventListener('mouseover', function(e) {
                const link = e.target.closest('.palette-button');
                if (!link) return;

                const formName = link.getAttribute('data-form');
                const buttonName = link.getAttribute('data-button-name');

                const form = document.forms[formName + 'Form'];
                if (!form) return;

                if (typeof window.MM_getButtonWithName === 'function') {
                    const button = window.MM_getButtonWithName(form, buttonName);
                    if (button && button.over) {
                        button.over();
                    }
                }
            });

            // Mouseout handler
            document.addEventListener('mouseout', function(e) {
                const link = e.target.closest('.palette-button');
                if (!link) return;

                const formName = link.getAttribute('data-form');
                const buttonName = link.getAttribute('data-button-name');

                const form = document.forms[formName + 'Form'];
                if (!form) return;

                if (typeof window.MM_getButtonWithName === 'function') {
                    const button = window.MM_getButtonWithName(form, buttonName);
                    if (button && button.out) {
                        button.out();
                    }
                }
            });
        },

        /**
         * Setup tooltip link click prevention
         * Handles: <a class="help-icon" href="#" data-tooltip="...">
         */
        setupTooltipLinks: function() {
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.help-icon, [data-tooltip]');
                if (!link) return;

                // Only prevent default if it's a # link
                if (link.getAttribute('href') === '#') {
                    e.preventDefault();
                }
            });
        }
    };

    /**
     * Initialize when DOM is ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            EventDelegation.init();
        });
    } else {
        EventDelegation.init();
    }

    // Export for potential programmatic use
    window.EventDelegation = EventDelegation;

})(window, document);
