/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import 'jquery';
import 'popper.js';
import 'bootstrap';

$(function () {

    $('.category-tree-chevron').on('click', function () {
        $(this).toggleClass('fa-chevron-down fa-chevron-right');
    });

    $('form[data-buy-offer]').on('submit', function (e) {

        e.preventDefault();

        var $form = $(this);
        var $btn = $form.find('button');

        $.post({
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $btn.addClass('disabled').attr('disabled', true);
            },
            success: function (data) {
                $btn.removeClass('disabled').attr('disabled', false);
                $('#cart-badge-items').html(data.items);
            },
        })
    });

    $('form[data-remove-item]').on('submit', function (e) {

        e.preventDefault();

        var $form = $(this);
        var $btn = $form.find('button');
        var $row = $btn.closest('tr');

        $.post({
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $btn.addClass('disabled').attr('disabled', true);
            },
            success: function (data) {
                $row.remove();
                $('#cart-badge-items').html(data.items);
            },
        })
    });

    $('table.shopping-cart-wrap select.quantity-select').on('change', function () {

        if ($(this).find('option:selected').hasClass('quantity-option-10')) {

            var $node = $(this).closest('td');

            $node.append($('<input type="text" class="form-control">'));
            $node.find('select').remove();
        }
    });


    function redirectWithFilterOptions() {

        var prices = $('#menu_filter .filter-by-price form')
            .serializeArray()
            .filter(function (elem) {
                return parseFloat(elem.value);
            });
        prices = $.param(prices);

        var features = $('#menu_filter .filter-by-feature form input:checked')
            .map(function () {
                return $(this).attr('name')
            })
            .toArray()
            .join('+');
        if (features) {
            features = 'fts=' + features;
        }

        var filters = [prices, features].filter(Boolean).join('&');

        if (filters) {
            filters = '?' + filters;
        }

        window.location.href = window.location.origin + window.location.pathname + filters;
    }

    $('#menu_filter .filter-by-feature input').on('input', redirectWithFilterOptions);
    $('#menu_filter .filter-by-price button').on('click', redirectWithFilterOptions);
});
