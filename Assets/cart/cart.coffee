$ ->
  $totalAmount = $(".total-amount")
  $discountedAmount = $(".discounted-amount")
  $(".quantity-selector").change ->
    $el = $(this)
    $row = $el.parents("tr")
    $amount = $row.find(".amount")
    id = $el.data("id")
    quantity = $el.val()
    runAction "CartBundle::Action::UpdateCartItem",
      id: id
      quantity: quantity
    , (resp) ->
      $.jGrowl resp.message
      if resp.success
        
        # update item amount price here
        $amount.text "$ " + resp.data.amount
        $totalAmount.text "$ " + resp.data.total_amount
        $discountedAmount.text "$ " + resp.data.discounted_total_amount


  $(".coupon-submit-btn").click ->
    couponCode = $(".coupon-code").val()
    runAction "CouponBundle::Action::ApplyCoupon",
      coupon_code: couponCode
    , (resp) ->
      
      # resp.data.discounted_total_amount
      # resp.data.total_amount
      if resp.success
        $.jGrowl resp.message
        $(".discounted-summary").removeClass "hide"
        $(".total-amount").text("$ " + resp.data.total_amount).parent().addClass "line-through"
        $(".discounted-amount").text "NT$ " + resp.data.discounted_total_amount
      else
        $(".discounted-summary").addClass "hide"
        $(".total-amount").parent().removeClass "line-through"
        $.jGrowl resp.message,
          theme: "error"



  $(".remove-cart-item").click ->
    $el = $(this)
    itemId = $(this).data("id")
    runAction "CartBundle::Action::RemoveCartItem",
      id: itemId
    , (resp) ->
      if resp.success
        $.jGrowl resp.message
        $el.parents("tr").fadeOut()
        $totalAmount.text "$ " + resp.data.total_amount
        $discountedAmount.text "$ " + resp.data.discounted_total_amount


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
