/**
 * PhpCollab - Modernized JavaScript Module
 * Refactored from legacy Macromedia/Dreamweaver code
 *
 * This file provides core functionality for:
 * - Toggle/collapse sections
 * - Checkbox management
 * - Button state management
 * - Cookie handling
 * - Popup windows
 */

(function(window, document) {
    'use strict';

    // ===================================================================
    // Cookie Management
    // ===================================================================

    /**
     * Check if cookies are supported
     */
    const hasCookies = (function() {
        try {
            document.cookie = 'cookieTest=1';
            const supported = document.cookie.indexOf('cookieTest=') !== -1;
            document.cookie = 'cookieTest=1; expires=Thu, 01 Jan 1970 00:00:00 GMT';
            return supported;
        } catch (e) {
            return false;
        }
    })();

    /**
     * Set a cookie
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number|string} hours - Expiration in hours or date string
     * @param {string} path - Cookie path
     */
    function setCookie(name, value, hours, path) {
        if (!hasCookies) return;

        let expires = '';
        if (hours) {
            const date = new Date();
            if (typeof hours === 'number') {
                date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            } else if (typeof hours === 'string') {
                expires = '; expires=' + hours;
            }
        }

        const pathStr = path ? '; path=' + path : '';
        document.cookie = name + '=' + encodeURIComponent(value) + expires + pathStr;
    }

    /**
     * Read a cookie value
     * @param {string} name - Cookie name
     * @returns {string} Cookie value or empty string
     */
    function readCookie(name) {
        const nameEQ = name + '=';
        const cookies = document.cookie.split(';');

        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.indexOf(nameEQ) === 0) {
                return decodeURIComponent(cookie.substring(nameEQ.length));
            }
        }
        return '';
    }

    // ===================================================================
    // Toggle/Collapse Functionality
    // ===================================================================

    /**
     * Toggle show/hide for collapsible modules
     * @param {string} divID - ID of the div to toggle
     * @param {string} theme - Theme name for image paths
     */
    function showHideModule(divID, theme) {
        const state = toggleFoldyPersistState(divID);
        const divElement = document.getElementById(divID);
        const toggleElement = document.getElementById(divID + 'Toggle');

        if (divElement && toggleElement) {
            if (state === 'collapse') {
                toggleElement.src = '../themes/' + theme + '/images/module_toggle_closed.gif';
                divElement.classList.add('toggle-hide');
            } else {
                toggleElement.src = '../themes/' + theme + '/images/module_toggle_open.gif';
                divElement.classList.remove('toggle-hide');
            }
        }
    }

    /**
     * Toggle and persist collapse state in cookie
     * @param {string} divID - ID of the div
     * @returns {string} New state ('expand' or 'collapse')
     */
    function toggleFoldyPersistState(divID) {
        const currentState = readCookie(divID);
        const newState = (currentState === 'expand' || currentState === '') ? 'collapse' : 'expand';
        setCookie(divID, newState, 8760, '/'); // 1 year
        return newState;
    }

    // ===================================================================
    // Checkbox Management
    // ===================================================================

    /**
     * Sync selectedItems array with actual checkbox states (for real HTML checkboxes)
     * @param {HTMLFormElement} form - The form
     */
    function MM_syncSelectedItems(form) {
        if (!form) return;

        form.selectedItems = [];
        const checkboxes = form.querySelectorAll('.checkbox-item:checked');

        checkboxes.forEach(function(checkbox) {
            const itemId = checkbox.getAttribute('data-item-id') || checkbox.value;
            if (itemId) {
                form.selectedItems.push(itemId);
            }
        });

        return form.selectedItems;
    }

    /**
     * Toggle a single checkbox item (Legacy - for backward compatibility with image-based checkboxes)
     * @param {HTMLFormElement} form - The form containing the checkbox
     * @param {string} itemName - Name of the item
     * @param {string} imageName - Name of the checkbox image (deprecated)
     * @param {string} theme - Theme name for image paths (deprecated)
     * @deprecated Use real HTML checkboxes with MM_syncSelectedItems instead
     */
    function MM_toggleItem(form, itemName, imageName, theme) {
        // For real checkboxes, this is handled by event delegation
        // This function is kept for backward compatibility
        MM_syncSelectedItems(form);
        MM_updateButtons2(form, form.selectedItems);
    }

    /**
     * Select all checkboxes in a form
     * @param {HTMLFormElement} form - The form
     * @param {string} theme - Theme name (deprecated - not used with real checkboxes)
     */
    function MM_selectAllItems(form, theme) {
        const checkboxes = form.querySelectorAll('.checkbox-item');
        checkboxes.forEach(function(checkbox) {
            if (!checkbox.disabled) {
                checkbox.checked = true;
            }
        });

        MM_syncSelectedItems(form);
        MM_updateButtons2(form, form.selectedItems);
    }

    /**
     * Deselect all checkboxes in a form
     * @param {HTMLFormElement} form - The form
     * @param {string} theme - Theme name (deprecated - not used with real checkboxes)
     */
    function MM_deselectAllItems(form, theme) {
        const checkboxes = form.querySelectorAll('.checkbox-item');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = false;
        });

        form.selectedItems = [];
        MM_updateButtons2(form, form.selectedItems);
    }

    /**
     * Toggle between select all and deselect all
     * @param {HTMLFormElement} form - The form
     * @param {string} theme - Theme name (deprecated - not used with real checkboxes)
     */
    function MM_toggleSelectedItems(form, theme) {
        MM_syncSelectedItems(form);

        const checkboxes = form.querySelectorAll('.checkbox-item');
        const checkedCount = form.querySelectorAll('.checkbox-item:checked').length;

        if (checkedCount === checkboxes.length && checkboxes.length > 0) {
            MM_deselectAllItems(form, theme);
        } else {
            MM_selectAllItems(form, theme);
        }
    }

    /**
     * Count disabled checkboxes in a form
     * @param {HTMLFormElement} form - The form
     * @returns {number} Count of disabled checkboxes
     */
    function MM_countDisabledCheckboxes(form) {
        const checkboxes = form.querySelectorAll('.checkbox-item:disabled');
        return checkboxes.length;
    }

    // ===================================================================
    // Button State Management
    // ===================================================================

    /**
     * Perform button action based on selection
     * @param {string} action - Action URL or JavaScript
     * @param {Array} selectedItems - Array of selected item IDs
     */
    function MM_doButtonAction(action, selectedItems) {
        // Check if action is JavaScript
        if (action.indexOf('javascript:') === 0 || action.indexOf('Javascript:') === 0) {
            // Remove 'javascript:' prefix and execute
            const jsCode = action.substring(action.indexOf(':') + 1);
            try {
                // Use Function constructor instead of eval for better scoping
                const fn = new Function('selectedItems', jsCode);
                fn(selectedItems);
            } catch (e) {
                console.error('Error executing button action:', e);
            }
            return;
        }

        // Check for function prefix
        let okay = true;
        if (action.indexOf('function:') === 0) {
            okay = false;
            const colonIdx = action.indexOf(':');
            if (colonIdx + 1 < action.length) {
                action = action.substring(colonIdx + 1);
                const commaIdx = action.indexOf(',');
                if (commaIdx !== -1 && commaIdx + 1 < action.length) {
                    const fnName = action.substring(0, commaIdx);
                    action = action.substring(commaIdx + 1);
                    const fn = window[fnName];
                    if (typeof fn === 'function') {
                        okay = fn(selectedItems);
                    }
                }
            }
        }

        if (okay) {
            // Build URL with selected items
            let url = action;
            if (selectedItems && selectedItems.length > 0) {
                const params = selectedItems.join('**');
                url += (url.indexOf('?') === -1 ? '?' : '&') + 'id=' + params;
            }
            window.location = url;
        }
    }

    /**
     * Update button states (simplified version)
     * @param {HTMLFormElement} form - The form
     */
    function MM_updateButtons(form) {
        MM_updateButtons2(form, []);
    }

    /**
     * Update button states based on selection
     * @param {HTMLFormElement} form - The form
     * @param {Array} selectedItems - Array of selected items
     */
    function MM_updateButtons2(form, selectedItems) {
        if (form.buttons) {
            for (let i = 0; i < form.buttons.length; i++) {
                const button = form.buttons[i];
                if (button && button.update) {
                    button.update(selectedItems);
                }
            }
        }
    }

    /**
     * Get a button by name
     * @param {HTMLFormElement} form - The form
     * @param {string} buttonName - Name of the button
     * @returns {Object|null} Button object or null
     */
    function MM_getButtonWithName(form, buttonName) {
        if (form.buttons) {
            for (let i = 0; i < form.buttons.length; i++) {
                const button = form.buttons[i];
                if (button.mName === buttonName) {
                    return button;
                }
            }
        }
        return null;
    }

    // ===================================================================
    // Button and Checkbox Classes
    // ===================================================================

    /**
     * Command Button Constructor
     * @constructor
     */
    function MMCommandButton(name, form, action, enabledImage, overImage, downImage,
                           disabledImage, enableOnNoSelection, enableOnSingleSelection,
                           enableOnMultipleSelection, enabledCheckSelectionJS, altText,
                           confirmation, confirmationMessage) {
        this.mName = name;
        this.mForm = form;
        this.mAction = action;
        this.mEnabledImage = enabledImage;
        this.mOverImage = overImage;
        this.mDisabledImage = disabledImage;
        this.mEnableOnNoSelection = enableOnNoSelection;
        this.mEnableOnSingleSelection = enableOnSingleSelection;
        this.mEnableOnMultipleSelection = enableOnMultipleSelection;
        this.mEnabledCheckSelectionJS = null;

        if (enabledCheckSelectionJS !== '') {
            this.mEnabledCheckSelectionJS = window[enabledCheckSelectionJS];
        }

        this.mAltText = altText;
        this.mConfirmation = confirmation;
        this.mConfirmationMessage = confirmationMessage;
        this.mEnabled = false;

        this.update = MMCommandButton_update;
        this.over = MMCommandButton_over;
        this.out = MMCommandButton_out;
        this.click = MMCommandButton_click;
    }

    /**
     * Update button state
     */
    function MMCommandButton_update(selectedItems) {
        const imgElement = document.getElementById(this.mName) || document[this.mName];
        if (!imgElement) return;

        if (this.mEnabledCheckSelectionJS &&
            typeof this.mEnabledCheckSelectionJS === 'function') {
            const isEnabled = this.mEnabledCheckSelectionJS(selectedItems);
            imgElement.src = isEnabled ? this.mEnabledImage : this.mDisabledImage;
            this.mEnabled = isEnabled;
        } else {
            const count = selectedItems.length;
            let enabled = false;

            if (count === 0) {
                enabled = this.mEnableOnNoSelection === true;
            } else if (count === 1) {
                enabled = this.mEnableOnSingleSelection === true;
            } else if (count > 1) {
                enabled = this.mEnableOnMultipleSelection === true;
            }

            imgElement.src = enabled ? this.mEnabledImage : this.mDisabledImage;
            this.mEnabled = enabled;
        }
    }

    /**
     * Handle button mouseover
     */
    function MMCommandButton_over() {
        if (this.mEnabled) {
            const imgElement = document.getElementById(this.mName) || document[this.mName];
            if (imgElement) {
                imgElement.src = this.mOverImage;
            }
        }
        // Note: swapText functionality removed as it's rarely used
        window.status = this.mAltText;
    }

    /**
     * Handle button mouseout
     */
    function MMCommandButton_out() {
        if (this.mEnabled) {
            const imgElement = document.getElementById(this.mName) || document[this.mName];
            if (imgElement) {
                imgElement.src = this.mEnabledImage;
            }
        }
        window.status = '';
    }

    /**
     * Handle button click
     */
    function MMCommandButton_click() {
        if (this.mEnabled) {
            if (this.mConfirmation && !confirm(this.mConfirmationMessage)) {
                return;
            }
            MM_doButtonAction(this.mAction, this.mForm.selectedItems);
        }
        window.status = '';
    }

    /**
     * Checkbox Constructor
     * @constructor
     */
    function MMCheckbox(name, form, imageName) {
        this.mName = name;
        this.mForm = form;
        this.mImageName = imageName;
    }

    // ===================================================================
    // Utility Functions
    // ===================================================================

    /**
     * Open a popup window
     * @param {string} loc - URL to open
     * @param {number} w - Width
     * @param {number} h - Height
     * @param {boolean} menubar - Show menubar
     */
    function popUp(loc, w, h, menubar) {
        w = w || 700;
        h = h || 500;
        const menubarStr = (menubar === true) ? 'menubar,' : '';

        const editorWin = window.open(
            loc,
            'editWin',
            menubarStr + 'resizable,scrollbars,width=' + w + ',height=' + h
        );

        if (editorWin) {
            editorWin.focus();
        }

        return editorWin;
    }

    /**
     * Submit form on Enter key
     * @param {HTMLFormElement} form - The form to submit
     * @param {Event} e - Keyboard event
     * @returns {boolean} False to prevent default
     */
    function submitOnEnter(form, e) {
        const event = e || window.event;
        const key = event.which || event.keyCode;

        if (key === 13) {
            if (form) form.submit();
            return false;
        }
        return true;
    }

    /**
     * Check maximum character length in input
     * @param {HTMLInputElement} obj - Input element
     * @returns {boolean} True if within limit
     */
    function checkMaxChars(obj) {
        if (obj.value.length >= obj.maxChars) {
            alert(obj.maxCharsError + ': ' + obj.maxChars);
            obj.value = obj.value.substr(0, obj.maxChars);
            return false;
        }
        return true;
    }

    /**
     * Focus and select an input field
     * @param {string} fld - Field ID or name
     */
    function focusAndSelect(fld) {
        const fldObj = document.getElementById(fld) || document.getElementsByName(fld)[0];
        if (fldObj) {
            fldObj.focus();
            fldObj.select();
        }
    }

    // ===================================================================
    // File/Folder Helper Functions (for file management)
    // ===================================================================

    function MM_countFilesFolders(selectedItems) {
        const result = { files: 0, folders: 0 };

        for (let i = 0; i < selectedItems.length; i++) {
            const ftypeElement = document.getElementById(selectedItems[i] + 'ftype');
            if (ftypeElement) {
                if (ftypeElement.value === 'file') {
                    result.files++;
                } else if (ftypeElement.value === 'folder') {
                    result.folders++;
                }
            }
        }

        return result;
    }

    function MM_oneFileOnly(selectedItems) {
        if (selectedItems.length !== 1) return false;

        const ftypeElement = document.getElementById(selectedItems[0] + 'ftype');
        return ftypeElement && ftypeElement.value === 'file';
    }

    function MM_atLeastOneFile(selectedItems) {
        for (let i = 0; i < selectedItems.length; i++) {
            const ftypeElement = document.getElementById(selectedItems[i] + 'ftype');
            if (ftypeElement && ftypeElement.value === 'file') {
                return true;
            }
        }
        return false;
    }

    // ===================================================================
    // Export to Global Scope (for backward compatibility)
    // ===================================================================

    window.hasCookies = hasCookies;
    window.setCookie = setCookie;
    window.readCookie = readCookie;
    window.showHideModule = showHideModule;
    window.toggleFoldyPersistState = toggleFoldyPersistState;
    window.MM_syncSelectedItems = MM_syncSelectedItems;
    window.MM_toggleItem = MM_toggleItem;
    window.MM_selectAllItems = MM_selectAllItems;
    window.MM_deselectAllItems = MM_deselectAllItems;
    window.MM_toggleSelectedItems = MM_toggleSelectedItems;
    window.MM_countDisabledCheckboxes = MM_countDisabledCheckboxes;
    window.MM_doButtonAction = MM_doButtonAction;
    window.MM_updateButtons = MM_updateButtons;
    window.MM_updateButtons2 = MM_updateButtons2;
    window.MM_getButtonWithName = MM_getButtonWithName;
    window.MMCommandButton = MMCommandButton;
    window.MMCheckbox = MMCheckbox;
    window.popUp = popUp;
    window.submitOnEnter = submitOnEnter;
    window.checkMaxChars = checkMaxChars;
    window.focusAndSelect = focusAndSelect;
    window.MM_countFilesFolders = MM_countFilesFolders;
    window.MM_oneFileOnly = MM_oneFileOnly;
    window.MM_atLeastOneFile = MM_atLeastOneFile;

})(window, document);
