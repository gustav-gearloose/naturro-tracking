/**
 * NaturRo Tracking Core
 * Implements a TrackingManager and diverse TrackingStrategies.
 * Adheres to SOLID principles:
 * - Single Responsibility: DOM parsing, Strategy Execution, Manager Routing are distinct.
 * - Open/Closed: New tracking services can be added without altering the Manager.
 */

(function() {
    'use strict';

    /**
     * Interface-like abstract class for Tracking Strategy
     */
    class TrackingStrategy {
        /**
         * Tracks a pageview
         */
        pageview() {
            throw new Error("Method 'pageview()' must be implemented.");
        }

        /**
         * Tracks a custom event
         * @param {string} name 
         * @param {Object} properties 
         */
        event(name, properties = {}) {
            throw new Error("Method 'event()' must be implemented.");
        }

        /**
         * Identify user
         * @param {string} userId 
         * @param {Object} traits 
         */
        identify(userId, traits = {}) {
            throw new Error("Method 'identify()' must be implemented.");
        }
    }

    /**
     * Rybbit Tracking Implementation
     */
    class RybbitStrategy extends TrackingStrategy {
        pageview() {
            if (window.rybbit && typeof window.rybbit.pageview === 'function') {
                window.rybbit.pageview();
            } else {
                console.warn('Rybbit: window.rybbit.pageview is not available');
            }
        }

        event(name, properties = {}) {
            if (window.rybbit && typeof window.rybbit.event === 'function') {
                window.rybbit.event(name, properties);
                console.debug(`[Rybbit] Tracked event: ${name}`, properties);
            } else {
                console.warn(`Rybbit: cannot track event ${name}. window.rybbit.event not available`);
            }
        }

        identify(userId, traits = {}) {
            if (window.rybbit && typeof window.rybbit.identify === 'function') {
                window.rybbit.identify(userId, traits);
            }
        }
    }

    /**
     * Central Manager to dispatch tracking calls to active strategies
     */
    class TrackingManager {
        constructor() {
            this.strategies = [];
        }

        /**
         * Register a new tracking strategy
         * @param {TrackingStrategy} strategy 
         */
        addStrategy(strategy) {
            if (!(strategy instanceof TrackingStrategy)) {
                console.warn('TrackingManager: Expected instance of TrackingStrategy');
                return;
            }
            this.strategies.push(strategy);
        }

        pageview() {
            this.strategies.forEach(s => s.pageview());
        }

        event(name, properties = {}) {
            this.strategies.forEach(s => s.event(name, properties));
        }

        identify(userId, traits = {}) {
            this.strategies.forEach(s => s.identify(userId, traits));
        }
    }

    /**
     * DOM Parser responsible for scanning `data-track-event` attributes
     * and routing them to the TrackingManager.
     */
    class TrackingDOMObserver {
        constructor(manager) {
            this.manager = manager;
            this.init();
        }

        init() {
            // Listen to clicks on document to catch dynamically added elements as well
            document.addEventListener('click', (e) => {
                // Find closest element with the data-track-event attribute
                const target = e.target.closest('[data-track-event]');
                if (!target) return;

                const eventName = target.getAttribute('data-track-event');
                if (!eventName) return;

                const properties = this.extractProperties(target);
                this.manager.event(eventName, properties);
            });
        }

        /**
         * Extracts properties from `data-track-prop-*` attributes
         */
        extractProperties(element) {
            const properties = {};
            const attrs = element.attributes;
            
            for (let i = 0; i < attrs.length; i++) {
                const attr = attrs[i];
                if (attr.name.startsWith('data-track-prop-')) {
                    const propName = attr.name.replace('data-track-prop-', '');
                    
                    // Attempt numeric conversion or keep as string
                    let value = attr.value;
                    if (!isNaN(value) && value.trim() !== '') {
                        value = Number(value);
                    }
                    
                    properties[propName] = value;
                }
            }
            return properties;
        }
    }

    // Initialize System
    document.addEventListener('DOMContentLoaded', () => {
        const manager = new TrackingManager();

        // Check config passed via wp_localize_script
        const config = window.naturroTrackingConfig || { services: { rybbit: true } };

        if (config.services.rybbit) {
            manager.addStrategy(new RybbitStrategy());
        }

        // Initialize DOM Observer
        new TrackingDOMObserver(manager);
        
        // Expose manager globally if manual tracking is needed from outside modules
        window.naturroTrackingManager = manager;
    });

})();
