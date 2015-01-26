$(document.body).ready ->
  $cartAnimation = $("<div/>").attr("id", "cart-animation").hide().appendTo(document.body)
  $(document.body).on "click", ".add-to-cart", ->
    animate = ->
      renableButton = ->
      $("#cart-animation").show()
      timeoutID = window.setTimeout(renableButton, 1500)
      addtocartWidth = elem.outerWidth() / 2
      addtocartHeight = elem.outerHeight() / 2
      addtocartLeft = elem.offset().left + addtocartWidth
      addtocartTop = $(elem).offset().top + addtocartHeight
      buttonAreaWidth = $("#cart-target").outerWidth()
      buttonAreaHeight = $("#cart-target").outerHeight()
      buttonAreaLeft = $("#cart-target").offset().left + buttonAreaWidth / 2 - $("#cart-animation").outerWidth() / 2
      buttonAreaTop = $("#cart-target").offset().top + buttonAreaWidth / 2 - $("#cart-animation").outerHeight() / 2
      path =
        start:
          x: addtocartLeft
          y: addtocartTop
          angle: 190.012
          length: 0.2

        end:
          x: buttonAreaLeft
          y: buttonAreaTop
          angle: 90.012
          length: 0.50

      $("#cart-animation").text $(".quantity").val()
      $("#cart-animation").animate
        path: new $.path.bezier(path)
      , 1200, ->
        $(elem).prop "disabled", false
        $("#cart-animation").fadeOut 500
        $(elem).closest("form").submit()

    elem = $(this)
    $(elem).prop "disabled", true
    e.preventDefault()
    animate()


# end doc ready
#
###
# add-to-cart form
# $.jGrowl(resp.message);


updateQuantitySelector = (typeId) ->
  return  unless typeId
  ProductAPI.getType typeId, (productType) ->
    $quantitySel = $(".product-quantity-selector").empty()
    quantity = parseInt(productType.quantity)
    if quantity
      if quantity > 9
        quantity = 9
      else quantity = 9  if quantity is -1
      i = 1

      while i <= quantity
        $("<option/>").text(i).val(i).appendTo $quantitySel
        i++
    else
      $("<option/>").text(0).val(0).appendTo $quantitySel

$form = $("#addToCartForm")
if $form.length
  a = Action.form($form,
    clear: false
    onSuccess: (resp) ->
      $("#cart-target").text resp.data.total_quantity  if resp.data.total_quantity
  )

# when product type changed, we should also update the quantity
$(".product-type-selector").change ->
  updateQuantitySelector $(this).val()

updateQuantitySelector $(".product-type-selector").val()
###
