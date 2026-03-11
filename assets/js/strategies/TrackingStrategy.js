/**
 * Tracking Strategy Interface
 * Abstract class to ensure all implementing strategies have identical APIs.
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

window.TrackingStrategy = TrackingStrategy;
