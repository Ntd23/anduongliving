'use strict'

$(document).ready(function () {
    var menuSelector = '.riorelax-mobile-nav'

    function setMenuState($menu, isOpen) {
        if (!$menu.length) {
            return
        }

        $menu.toggleClass('is-open', isOpen)
        $menu.find('.riorelax-mobile-panel').attr('aria-hidden', isOpen ? 'false' : 'true')
        $menu.find('[data-mobile-menu-toggle]').attr('aria-expanded', isOpen ? 'true' : 'false')
        $('body').toggleClass('mobile-menu-open', isOpen)
    }

    function closeAllMenus() {
        $(menuSelector).each(function () {
            setMenuState($(this), false)
        })
    }

    $(document)
        .on('click', '[data-mobile-menu-toggle]', function () {
            var $menu = $(this).closest(menuSelector)
            var shouldOpen = !$menu.hasClass('is-open')

            closeAllMenus()
            setMenuState($menu, shouldOpen)
        })
        .on('click', '[data-mobile-menu-close]', function () {
            closeAllMenus()
        })
        .on('click', '.riorelax-mobile-panel a', function () {
            closeAllMenus()
        })

    $(document).on('keyup', function (event) {
        if (event.key === 'Escape') {
            closeAllMenus()
        }
    })
})
