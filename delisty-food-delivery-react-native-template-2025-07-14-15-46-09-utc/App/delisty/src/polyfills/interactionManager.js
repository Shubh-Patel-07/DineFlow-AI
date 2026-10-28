import * as ReactNative from "react-native";

const interactionManagerPolyfill = {
    runAfterInteractions: (task) => {
        const handle = typeof requestIdleCallback === "function" 
            ? requestIdleCallback(task) 
            : setTimeout(task, 0);
        return {
            cancel: () => {
                if (typeof cancelIdleCallback === "function") cancelIdleCallback(handle);
                else clearTimeout(handle);
            },
        };
    },
    createInteractionHandle: () => 1,
    clearInteractionHandle: () => {},
    setDeadline: () => {},
    addListener: () => ({ remove: () => {} }),
};

try {
    Object.defineProperty(ReactNative, "InteractionManager", {
        configurable: true,
        enumerable: true,
        writable: true,
        value: interactionManagerPolyfill,
    });
} catch (e) {
    try {
        ReactNative.InteractionManager = interactionManagerPolyfill;
    } catch (_) {}
}

export default interactionManagerPolyfill;
