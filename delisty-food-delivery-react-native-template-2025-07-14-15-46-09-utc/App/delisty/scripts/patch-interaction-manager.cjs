const fs = require('fs');
const path = require('path');

const rnIndexPath = path.resolve(__dirname, '..', 'node_modules', 'react-native', 'index.js');

if (fs.existsSync(rnIndexPath)) {
    let content = fs.readFileSync(rnIndexPath, 'utf8');
    if (content.includes('InteractionManager has been removed from react-native core')) {
        const target = `  Object.defineProperty(module.exports, 'InteractionManager', {
    configurable: true,
    get() {
      invariant(
        false,
        'InteractionManager has been removed from react-native core. ' +
          'Please refactor long tasks into smaller ones, and use ' +
          "'requestIdleCallback' instead.",
      );
    },
  });`;

        const replacement = `  const _interactionManagerPolyfill = {
    runAfterInteractions: (task) => {
      const handle = typeof requestIdleCallback === "function" ? requestIdleCallback(task) : setTimeout(task, 0);
      return { cancel: () => (typeof cancelIdleCallback === "function" ? cancelIdleCallback(handle) : clearTimeout(handle)) };
    },
    createInteractionHandle: () => 1,
    clearInteractionHandle: () => {},
    setDeadline: () => {},
    addListener: () => ({ remove: () => {} }),
  };
  Object.defineProperty(module.exports, 'InteractionManager', {
    configurable: true,
    enumerable: true,
    writable: true,
    value: _interactionManagerPolyfill,
  });`;

        content = content.replace(target, replacement);
        fs.writeFileSync(rnIndexPath, content, 'utf8');
        console.log('[DineFlow AI] InteractionManager polyfill successfully applied to react-native');
    }
}
