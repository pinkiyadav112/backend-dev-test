/**
 * All kotwBlocks herlpers methods and globals.
 * These functions are added earlier in the DOM.
 */

window.kotwBlocks = window.kotwBlocks || {};

// Add the kotw blocks to the global window object.
if (KOTW.blocks.length > 0) {
    KOTW.blocks.forEach(block => {
        kotwBlocks[block.name] = block;
    });
    window.kotwBlocks = kotwBlocks;
}
