/**
 * Rybbit Analytics Strategy Implementation
 */
class RybbitStrategy extends window.TrackingStrategy {
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

window.RybbitStrategy = RybbitStrategy;
