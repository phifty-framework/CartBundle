$ ->
  updateCartDetails = (summary) ->
    return if not summary or not summary.total_amount
    $totalAmount.text "NT$ " + summary.total_amount
    $shippingCost.text "NT$ " + summary.shipping_cost
    $discountAmount.text "NT$ " + summary.discount_amount
    $discountedTotalAmount.text "NT$ " + summary.discounted_total_amount
    
    # With discount
    unless summary.discounted_total_amount is summary.total_amount
      $totalAmount.parent().addClass "line-through"
      $discountedSummary.removeClass "hide"
    else
      # Without discount
      $totalAmount.parent().removeClass "line-through"
      $discountedSummary.addClass "hide"
      $couponInput.val "" # clear coupon code
  $cartSummary = $(".cart-summary")
  $totalAmount = $(".total-amount")
  $discountedTotalAmount = $(".discounted-total-amount")
  $discountAmount = $(".discount-amount")
  $discountedSummary = $(".discounted-summary")
  $shippingCost = $(".shipping-cost")
  $couponInput = $(".coupon-code")

  $(document.body).on "change", ".quantity-selector", ->
    $el = $(this)
    $row = $el.parents("tr")
    $amount = $row.find(".amount")
    id = $el.data("id")
    quantity = $el.val()
    runAction "CartBundle::Action::UpdateCartItem",
      id: id
      quantity: quantity
    , (resp) ->
      
      # $.jGrowl(resp.message);
      if resp.success
        
        # update item amount price here
        $amount.text "$ " + resp.data.amount # the item amount.
        updateCartDetails resp.data  if resp.data

  $(document.body).on "click", ".coupon-submit-btn", ->
    couponCode = $(".coupon-code").val()
    runAction "CartBundle::Action::ApplyCoupon",
      coupon_code: couponCode
    , (resp) ->
      
      # resp.data.discounted_total_amount
      # resp.data.total_amount
      if resp.success
        $.jGrowl resp.message
        updateCartDetails resp.data
      else
        updateCartDetails resp.data
        $couponInput.val ""
        $.jGrowl resp.message,
          theme: "error"

  $(document.body).on "click", ".remove-cart-item", ->
    $el = $(this)
    itemId = $(this).data("id")
    runAction "CartBundle::Action::RemoveCartItem",
      id: itemId
    , (resp) ->
      if resp.success
        # $.jGrowl(resp.message);
        $el.parents("tr").fadeOut()
        updateCartDetails resp.data

###
updateQuantitySelector = (typeId) ->
  $quantitySel = $(".quantity").empty()
  return  unless typeId
  ProductAPI.getType typeId, (productType) ->
    quantity = parseInt(productType.quantity)
    if quantity > 9
      quantity = 9
    else quantity = 9  if quantity is -1
    i = 1

    while i <= quantity
      $("<option/>").text(i).val(i).appendTo $quantitySel
      i++

# when product type changed, we should also update the quantity
$(".product-type").change ->
  updateQuantitySelector $(this).val()
updateQuantitySelector $(".product-type").val()
###
