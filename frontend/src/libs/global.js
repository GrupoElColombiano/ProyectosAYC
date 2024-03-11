import $ from 'cash-dom'

$(function () {
  menu()
  tabs()
  accordion()
  sharedButtons()
  modal()
  mobileMenu()
})

function menu() {
  $('.openMenu').on('click', function () {})
}

function tabs() {
  $('#tabsNav > .tabs-nav-item').on('click', function () {
    $('#tabsNav > .tabs-nav-item').removeClass('active')

    $(this).addClass('active')

    var tab = $(this).data('tab')

    $('#tabsContent > .tabs-content').removeClass('active').hide()

    var item = $('#tabsContent > .tabs-content[data-tab="' + tab + '"]').show()

    setTimeout(function () {
      item.addClass('active')
    }, 200)
  })
}

function accordion() {
  $('.accordion-item > .accordion-title').on('click', function () {
    var item = $(this).parent()

    if (item.hasClass('active')) {
      item.removeClass('active')
    } else {
      $('.accordion-item').removeClass('active')
      item.addClass('active')
    }
  })
}

function sharedButtons() {
  $('#shared').on('click', function () {
    var item = $(this).find('.share-menu')

    if (item.hasClass('active')) {
      item.removeClass('active')
    } else {
      $('.share-item').removeClass('active')
      item.show()

      setTimeout(function () {
        item.addClass('active')
      }, 100)
    }
  })
}

function modal() {
  $('[data-modal]').on('click', function () {
    var modal = $(this).data('modal')

    $('#' + modal).show()

    setTimeout(function () {
      $('#' + modal).addClass('active')
      $('html, body').css('overflow', 'hidden')
    }, 100)
  })

  $('[data-close]').on('click', function () {
    var modal = $(this).data('close')
    $('html, body').css('overflow', 'auto')

    $('#' + modal).removeClass('active')

    setTimeout(function () {
      $('#' + modal).hide()
    }, 100)
  })

  // keyup esc close modal javascript vanilla
  document.addEventListener('keyup', function (e) {
    if (e.key === 'Escape') {
      var modal = $('.modal.active').attr('id')
      $('html, body').css('overflow', 'auto')

      $('#' + modal).removeClass('active')

      setTimeout(function () {
        $('#' + modal).hide()
      }, 100)
    }
  })
}

function mobileMenu() {
  $('.toogleMenu').on('click', function () {
    var menu = $('#menuToggle')

    if (menu.hasClass('active')) {
      menu.removeClass('active')
      $('html, body').css('overflow', 'auto')
    } else {
      menu.addClass('active')
      $('html, body').css('overflow', 'hidden')
    }
  })
}
