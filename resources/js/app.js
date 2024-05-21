import './bootstrap';

import Masonry from 'masonry-layout';


document.addEventListener('DOMContentLoaded', function() {
    new Masonry('#gallery', {
        itemSelector: '.gallery-item',
        columnWidth: '.gallery-item',
        percentPosition: true
    });
});
