/**
 * Main Initialization for NaturRo Tracking System
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', () => {
        const manager = new window.TrackingManager();

        // Check config passed via wp_localize_script
        const config = window.naturroTrackingConfig || { services: { rybbit: false } };

        if (config.services.rybbit) {
            manager.addStrategy(new window.RybbitStrategy());
        }

        // Initialize DOM Observer
        new window.TrackingDOMObserver(manager);
        
        // Expose generically
        window.naturroTrackingManager = manager;
    });

})();
