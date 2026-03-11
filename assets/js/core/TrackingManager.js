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
        if (!(strategy instanceof window.TrackingStrategy)) {
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

window.TrackingManager = TrackingManager;
