class ExampleBlock {
    constructor(blockID) {
        this.id = blockID
        this.class = 'kotw-block-example'
        this.dom = document.querySelector(`#${this.id}`)
        this.state = {}
        this.state.data = JSON.parse(
            this.dom.getAttribute('data-state'),
        )

        this.listeners = [
            {
                selectors: ['.drinks .drink'],
                event: 'click',
                handler: this.getDrink,
            },
        ]


        // Register the events.
        kotwBlocks__registerEvents(this)
    }

    /**
     * This should be use to return any saved values in the state or
     * any information about this block.
     * This will return "this"
     *
     * @returns {*}
     */
    getObject() {
        return window.kotwBlocks[this.class][this.id] // This will set/get the updated data from the global object.
    }


    // Events Handlers.
    // You should register all event handlers here.
    getDrink(e){
        this.dom.querySelector('.drinkName p span').innerHTML = e.target.innerHTML;
    }
}




// Register the whole block.
kotwBlocks__addBlock('kotw-block-example', ExampleBlock)
