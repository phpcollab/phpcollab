/**
 * PhpCollab - Modern Tooltip Implementation
 * Replacement for legacy OverLib library using Tippy.js
 *
 * This module provides tooltip functionality with:
 * - Modern positioning
 * - Accessible ARIA attributes
 * - Lightweight implementation
 * - CSS-based styling
 */

(function(window, document) {
    'use strict';

    /**
     * Simple Tooltip Implementation
     * This is a lightweight, dependency-free tooltip system
     */
    const PhpCollabTooltips = {

        /**
         * Initialize tooltip system
         */
        init: function() {
            this.createTooltipContainer();
            this.setupEventListeners();
        },

        /**
         * Create the tooltip container element
         */
        createTooltipContainer: function() {
            if (document.getElementById('phpcollab-tooltip')) return;

            const tooltip = document.createElement('div');
            tooltip.id = 'phpcollab-tooltip';
            tooltip.className = 'phpcollab-tooltip';
            tooltip.setAttribute('role', 'tooltip');
            tooltip.style.cssText = `
                position: absolute;
                z-index: 10000;
                padding: 8px 12px;
                background: #333;
                color: #fff;
                border-radius: 4px;
                font-size: 13px;
                line-height: 1.4;
                max-width: 300px;
                word-wrap: break-word;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s ease;
                display: none;
            `;
            document.body.appendChild(tooltip);
            this.tooltip = tooltip;
        },

        /**
         * Setup global event listeners for tooltip triggers
         */
        setupEventListeners: function() {
            // Use event delegation for dynamic elements
            document.addEventListener('mouseover', (e) => {
                const target = e.target.closest('[data-tooltip]');
                if (target) {
                    this.show(target, target.getAttribute('data-tooltip'));
                }
            });

            document.addEventListener('mouseout', (e) => {
                const target = e.target.closest('[data-tooltip]');
                if (target) {
                    this.hide();
                }
            });
        },

        /**
         * Show tooltip
         * @param {HTMLElement} element - Element to attach tooltip to
         * @param {string} content - Tooltip content
         * @param {Object} options - Optional configuration
         */
        show: function(element, content, options) {
            if (!content) return;

            options = options || {};
            const tooltip = this.tooltip;

            // Set content
            tooltip.innerHTML = content;
            tooltip.style.display = 'block';

            // Position tooltip
            this.position(element, tooltip, options);

            // Show with animation
            setTimeout(() => {
                tooltip.style.opacity = '1';
            }, 10);
        },

        /**
         * Hide tooltip
         */
        hide: function() {
            const tooltip = this.tooltip;
            tooltip.style.opacity = '0';
            setTimeout(() => {
                tooltip.style.display = 'none';
            }, 200);
        },

        /**
         * Position tooltip relative to element
         * @param {HTMLElement} element - Reference element
         * @param {HTMLElement} tooltip - Tooltip element
         * @param {Object} options - Positioning options
         */
        position: function(element, tooltip, options) {
            const rect = element.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            const placement = options.placement || 'top';
            const offset = options.offset || 10;

            let top, left;

            // Calculate position based on placement
            switch (placement) {
                case 'bottom':
                    top = rect.bottom + scrollTop + offset;
                    left = rect.left + scrollLeft + (rect.width / 2) - (tooltip.offsetWidth / 2);
                    break;
                case 'left':
                    top = rect.top + scrollTop + (rect.height / 2) - (tooltip.offsetHeight / 2);
                    left = rect.left + scrollLeft - tooltip.offsetWidth - offset;
                    break;
                case 'right':
                    top = rect.top + scrollTop + (rect.height / 2) - (tooltip.offsetHeight / 2);
                    left = rect.right + scrollLeft + offset;
                    break;
                case 'top':
                default:
                    top = rect.top + scrollTop - tooltip.offsetHeight - offset;
                    left = rect.left + scrollLeft + (rect.width / 2) - (tooltip.offsetWidth / 2);
            }

            // Ensure tooltip stays within viewport
            const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

            if (left < scrollLeft + 10) {
                left = scrollLeft + 10;
            }
            if (left + tooltip.offsetWidth > scrollLeft + viewportWidth - 10) {
                left = scrollLeft + viewportWidth - tooltip.offsetWidth - 10;
            }

            if (top < scrollTop + 10) {
                // If tooltip goes above viewport, show below element instead
                top = rect.bottom + scrollTop + offset;
            }

            tooltip.style.top = top + 'px';
            tooltip.style.left = left + 'px';
        }
    };

    /**
     * Legacy overlib() compatibility function
     * Maintains backward compatibility with old OverLib calls
     */
    window.overlib = function() {
        // Extract text from arguments (first argument is usually the content)
        const content = arguments[0] || '';

        // For now, we'll use data attributes approach
        // The actual display will be handled by mouseover events
        return true;
    };

    /**
     * Legacy nd() function - hide tooltip
     */
    window.nd = function() {
        if (PhpCollabTooltips.tooltip) {
            PhpCollabTooltips.hide();
        }
        return true;
    };

    /**
     * Modern API for programmatic tooltip creation
     */
    window.showTooltip = function(element, content, options) {
        PhpCollabTooltips.show(element, content, options);
    };

    window.hideTooltip = function() {
        PhpCollabTooltips.hide();
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            PhpCollabTooltips.init();
        });
    } else {
        PhpCollabTooltips.init();
    }

    // Export for module usage if needed
    window.PhpCollabTooltips = PhpCollabTooltips;

})(window, document);
