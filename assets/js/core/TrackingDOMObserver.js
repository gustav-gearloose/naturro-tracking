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
        // Listen to clicks on document
        document.addEventListener('click', (e) => {
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

window.TrackingDOMObserver = TrackingDOMObserver;
