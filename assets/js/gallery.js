// https://teamtreehouse.com/community/how-to-create-gallery-using-custom-field-in-wordpress

jQuery(document).ready(function ($) {

  // Instantiates the variable that holds the media library frame.
  var meta_image_frame
  var $image_gallery_ids = $('#product_image_gallery')

  // Runs when the image button is clicked. You need to insert ID of your button
  $('#add-to-gallery').click(function (e) {

    // Prevents the default action from occuring.
    e.preventDefault()

    // If the frame already exists, re-open it.
    if (meta_image_frame) {
      meta_image_frame.open()
      return
    }

    // Sets up the media library frame
    meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
      title: 'Add images to product gallery',
      multiple: 'add',
      button: { text: 'Add to gallery' },
      library: { type: 'image' }
    })

    // Runs when an image is selected. You need to insert ID of input field
    meta_image_frame.on('select', function () {
      var selection = meta_image_frame.state().get('selection')
      var attachment_ids = $image_gallery_ids.val()
      selection.map(function (attachment) {
        attachment = attachment.toJSON()
        if (attachment.id) {
          attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id
          var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url

          $('#product-images').append(
            '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image +
            '" /><a href="#" class="delete">Delete</a></li>'
          )
        }
      })
      $image_gallery_ids.val(attachment_ids)
    })

    // Opens the media library frame.
    meta_image_frame.open()
  })

  // Remove images.
  $('#product_images_container').on('click', 'a.delete', function () {
    $(this).closest('li.image').remove()

    var attachment_ids = ''

    $('#product_images_container').find('ul li.image').css('cursor', 'default').each(function () {
      var attachment_id = $(this).attr('data-attachment_id')
      attachment_ids = attachment_ids + attachment_id + ','
    })

    $image_gallery_ids.val(attachment_ids)

    return false
  })
})