/**
 * Design Cart Woo Timelines
 * Author: Paweł Nosko — https://www.designcart.pl/pawel-nosko.html
 * Company: Design Cart — https://www.designcart.pl/
 */
(function ($) {
  'use strict';

  var cfg = window.dcwtAdmin || {};
  var timer = null;

  function escapeHtml(value) {
    return String(value == null ? '' : value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function reindex() {
    $('[data-product-list] [data-product-row]').each(function (i) {
      $(this).find('[name]').each(function () {
        this.name = this.name.replace(/products\[\d+\]/, 'products[' + i + ']');
      });
    });
    $('[data-empty-hint]').prop('hidden', $('[data-product-list] [data-product-row]').length > 0);
  }

  function productRow(item, idx) {
    var thumb = item.thumb
      ? '<img class="dcwt-product__thumb" src="' + escapeHtml(item.thumb) + '" alt="" />'
      : '<span class="dcwt-product__thumb dcwt-product__thumb--empty"></span>';
    return (
      '<li class="dcwt-product" data-product-row>' +
      '<span class="dashicons dashicons-move dcwt-product__handle" aria-hidden="true"></span>' +
      thumb +
      '<div class="dcwt-product__body">' +
      '<input type="hidden" name="products[' + idx + '][id]" value="' + escapeHtml(item.id) + '" />' +
      '<strong class="dcwt-product__title"></strong>' +
      '<div class="dc-row dc-row--2">' +
      '<div class="dc-field"><label class="dc-label">' + escapeHtml(cfg.i18n.date) + '</label>' +
      '<input class="dc-input" type="date" name="products[' + idx + '][date]" value="' + escapeHtml(item.date || '') + '" /></div>' +
      '<div class="dc-field"><label class="dc-label">' + escapeHtml(cfg.i18n.dateLabel) + '</label>' +
      '<input class="dc-input" type="text" name="products[' + idx + '][date_label]" value="' + escapeHtml(item.date_label || '') + '" /></div>' +
      '</div></div>' +
      '<button type="button" class="button-link-delete dcwt-product__remove" data-remove-product aria-label="' + escapeHtml(cfg.i18n.remove) + '">' +
      '<span class="dashicons dashicons-trash"></span></button></li>'
    );
  }

  function addProduct(item) {
    var list = $('[data-product-list]');
    if (list.find('input[name$="[id]"][value="' + item.id + '"]').length) {
      return;
    }
    var html = productRow(item, list.children().length);
    var $row = $(html);
    $row.find('.dcwt-product__title').text(item.name);
    list.append($row);
    reindex();
  }

  function existingProducts() {
    var rows = [];
    $('[data-product-list] [data-product-row]').each(function () {
      rows.push({
        id: $(this).find('input[name$="[id]"]').val(),
        date: $(this).find('input[name$="[date]"]').val(),
        date_label: $(this).find('input[name$="[date_label]"]').val(),
      });
    });
    return rows;
  }

  function toggleSource() {
    var source = $('input[name="source"]:checked').val();
    $('[data-source-category]').toggle(source === 'category');
  }

  function toggleThumbBorder() {
    $('[data-thumb-border-fields]').toggle($('input[name="thumb_border"]').is(':checked'));
  }

  $(function () {
    toggleSource();
    toggleThumbBorder();
    $(document).on('change', 'input[name="source"]', toggleSource);
    $(document).on('change', 'input[name="thumb_border"]', toggleThumbBorder);

    var list = $('[data-product-list]');
    if (list.length && $.fn.sortable) {
      list.sortable({
        handle: '.dcwt-product__handle',
        update: reindex,
      });
    }

    $(document).on('click', '[data-remove-product]', function () {
      $(this).closest('[data-product-row]').remove();
      reindex();
    });

    var $input = $('[data-search-input]');
    var $results = $('[data-search-results]');

    $input.on('input', function () {
      var q = $.trim(this.value);
      clearTimeout(timer);
      if (q.length < 2) {
        $results.prop('hidden', true).empty();
        return;
      }
      timer = setTimeout(function () {
        $.getJSON(cfg.ajax, { action: 'dcwt_search_products', nonce: cfg.nonce, q: q })
          .done(function (res) {
            $results.empty();
            if (!res.success || !res.data.length) {
              $results.prop('hidden', true);
              return;
            }
            res.data.forEach(function (item) {
              var thumb = item.thumb
                ? '<img class="dcwt-search__thumb" src="' + escapeHtml(item.thumb) + '" alt="" />'
                : '<span class="dcwt-search__thumb"></span>';
              var $btn = $('<button type="button" class="dcwt-search__item"></button>');
              $btn.append(thumb).append($('<span></span>').text(item.name));
              $btn.on('click', function () {
                addProduct(item);
                $input.val('');
                $results.prop('hidden', true).empty();
              });
              $results.append($('<li></li>').append($btn));
            });
            $results.prop('hidden', false);
          });
      }, 220);
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('[data-product-search]').length) {
        $results.prop('hidden', true);
      }
    });

    $('[data-load-source]').on('click', function () {
      var $btn = $(this);
      $btn.prop('disabled', true);
      $.post(cfg.ajax, {
        action: 'dcwt_load_source',
        nonce: cfg.nonce,
        source: $('input[name="source"]:checked').val(),
        category_id: $('select[name="category_id"]').val(),
        limit: $('input[name="limit"]').val(),
        products: JSON.stringify(existingProducts()),
      }).done(function (res) {
        if (res.success) {
          list.html(res.data.html);
          reindex();
        }
      }).always(function () {
        $btn.prop('disabled', false);
      });
    });

    $(document).on('click', '[data-copy-shortcode]', function () {
      var text = this.getAttribute('data-copy-shortcode') || '';
      if (!text || !navigator.clipboard) {
        return;
      }
      var btn = this;
      navigator.clipboard.writeText(text).then(function () {
        btn.classList.add('is-copied');
        window.setTimeout(function () { btn.classList.remove('is-copied'); }, 1200);
      });
    });

    $(document).on('submit', '[data-confirm-delete]', function (e) {
      if (!window.confirm(cfg.i18n.confirmDel || '')) {
        e.preventDefault();
      }
    });
  });
})(jQuery);
