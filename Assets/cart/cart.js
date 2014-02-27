$(function() {
    var $totalAmount = $('.total-amount');
    var $discountedAmount = $('.discounted-amount');

    $('.quantity-selector').change(function() {
        var $el = $(this);
        var $row = $el.parents('tr');
        var $amount = $row.find('.amount');

        var id = $el.data('id');
        var quantity = $el.val();
        runAction('CartBundle::Action::UpdateCartItem', { id: id, quantity: quantity }, function(resp) {
            $.jGrowl(resp.message);
            if ( resp.success ) {
                // update item amount price here
                $amount.text( '$ ' + resp.data.amount );
                $totalAmount.text( '$ ' + resp.data.total_amount );
                $discountedAmount.text( '$ ' + resp.data.discounted_amount );
            }
        });
    });

    $('.coupon-submit-btn').click(function() {
        var couponCode = $('.coupon-code').val();
        runAction('CouponBundle::Action::ApplyCoupon',{ coupon_code: couponCode }, function(resp) {
            // resp.data.discounted_amount
            // resp.data.total_amount
            if ( resp.success ) {
                $.jGrowl(resp.message);
                $('.discounted-summary').removeClass('hide');
                $('.total-amount').text( '$ ' + resp.data.total_amount ).parent().addClass('line-through');
                $('.discounted-amount').text('NT$ ' + resp.data.discounted_amount );
            } else {
                $('.discounted-summary').addClass('hide');
                $('.total-amount').parent().removeClass('line-through');
                $.jGrowl(resp.message, { theme: 'error' });
            }
        });
    });

    $('.remove-cart-item').click(function() {
        var $el = $(this);
        var itemId = $(this).data('id');
        runAction('CartBundle::Action::RemoveCartItem',{ id: itemId },function(resp) {
            if ( resp.success ) {
                $.jGrowl(resp.message);
                $el.parents('tr').fadeOut();
                $totalAmount.text( '$ ' + resp.data.total_amount );
                $discountedAmount.text( '$ ' + resp.data.discounted_amount );
            }
        });
    });
});
