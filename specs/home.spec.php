<?php
describe('The home page', function() {
    it('should have a greeting', function() {
        $this->getPage('http://localhost:4000');
        $greeting = $this->findElementById('greeting');
        assert($greeting->getText() === "Hello", "should be Hello");
    });
});
