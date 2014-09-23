function debug_log() {
console.log("sites:");
console.log(sites);
console.log("per page:");
console.log(per_page);

console.log("tr map:");
console.log(tr_map);

console.log("this_page:");
console.log(this_page);

console.log("site idx:");
console.log(site_idx);

console.log("this_cat:");
console.log(this_cat);

console.log("cats:");
console.log(cats);

console.log("selection:");
console.log(selection);

console.log("images:");
console.log(images);
}

// get image thumbnail url
function get_image_thumb(img) {
    if (img.derivatives == undefined)
        img_src = img.tn_url;
    else
        img_src = img.derivatives.thumb.url;
    return img_src;
}

// toggle visual selection
function toggle_selection(id) {
    found = -1;
    $.each(selection, function(idx, el) {
        if (el.id == id) {
            found = idx;
            return false;
        }
    });
    if (found >= 0)
        selection.splice(found, 1);
    else
        selection.push(images[String(id)]);
    $('ul.image-selector > li[title="'+id+'"]').toggleClass('selected');
}

// set visual message
function set_messsage(msg, type) {
    $('div.messages-section ul').empty();
    $('div.messages-section ul').append('<li class="'+type+'">'+msg+'</li>');
    $('div.messages-section').show();
}

// clear and hide visual messages
function hide_messages() {
    $('div.messages-section ul').empty();
    $('div.messages-section').hide();
}

// de/activate loading message
function set_loading(loading) {
    if (!loading) {
        hide_messages();
    } else {
        set_messsage(tr_map['Loading...'], 'info');
    }
}

function hide_options(clear_selection, clear_categories) {
    $('div.images-section').hide();
    $('div.style-section').hide();
    $('div.confirmation-section').hide();
    if (clear_selection)
        $('div.page-selection ol').empty();
    if (clear_categories)
        $('select[name="category"]').empty();
}

// load categories for current selected site
function refresh_site() {
    set_loading(true); 
    images = new Array();
    selection = new Array();
    cats = new Array();

    hide_options(true,true);

    site_idx=$('select[name="site"]').val();
    $.ajax({
        url: 'query_remote_pwg.php',
        dataType: 'json',
        data: {"__url__": site_idx, "format": "json", "method": "pwg.categories.getList", "recursive": true, "page": 0, "per_page": 200},
        success: function(data, textStatus) {
            if ((data == undefined) || data["stat"] != "ok") {
                set_messsage(tr_map["Error while reading from"]+" "+sites[site_idx] + ". " +
                tr_map["Please verify PiwigoMedia\'s configuration and try again."], 'error');
                return;
            }
            cats = new Array();
            $.each(data["result"]["categories"], function(idx, el) {
                cats[String(el["id"])] = el;
            });

            $('select[name="category"]').empty();
            $.each(data["result"]["categories"], function(idx, el) {
                uppercats = el["uppercats"].split(",");
                uppercats_names = new Array();
                $.each(uppercats, function(idx, n) {
                    uppercats_names.push(cats[String(n)]["name"]);
                });
                cat_name = uppercats_names.join("/");
                $('select[name="category"]').append("<option value='"+el["id"]+"'>"+cat_name+"</option>");
            });

            $('ul.image-selector').empty();
            selection = new Array();
            set_loading(false);
        },
        error: function(jqXHR, textStatus, errorThrown ) {
            hide_messages();
            set_messsage(textStatus, 'error');
        }
    });
}

// load images for current selected category
function refresh_category() {
    images = new Array();
    hide_options(false,false);

    set_loading(true);
    cat_idx=$('select[name="category"]').val();
    if (this_cat != cat_idx) {
        selection = new Array();
        this_page = 0;
    }
    $.ajax({
        url: 'query_remote_pwg.php',
        dataType: 'json',
        data: {"__url__": site_idx, "format": "json", "method": "pwg.categories.getImages", "cat_id": cat_idx, "page": this_page, "per_page": per_page},
        success: function(data) {
            if ((data == undefined) || data["stat"] != "ok") {
                set_messsage(tr_map["Error reading image information, please try again."], 'error');
                return;
            }

            $('ul.image-selector').empty();
            if (data["result"]["images"]["_content"] == undefined)
                images_ = data["result"]["images"];
            else
                images_ = data["result"]["images"]["_content"];
            $.each(images_, function(idx, el) {
                images[String(el["id"])] = el;
                $('ul.image-selector').append(
                    '<li title="'+el.id+'">'+
                        '<img src="'+get_image_thumb(el)+'" '+
                        'onclick="toggle_selection(\''+el.id+'\');" />'+
                    '</li>'
                );
            });

            $('div.page-selection ol').empty();
            if (images.length > 0) {
                pages = Math.ceil(cats[String(cat_idx)]["nb_images"]/per_page);
                for (i=0; i<pages; i++) {
                    li = $('<li><span onclick="this_page='+i+'; refresh_category();">'+(i+1)+'</span></li>');
                    if (i == this_page) li.addClass('selected');
                    if (i+1 == pages) li.addClass('last');
                    $('div.page-selection ol').append(li);
                }
            }


            $.each(selection, function(idx, el) {
                $('ul.image-selector > li[title="'+el.id+'"]').toggleClass('selected');
            });
            this_cat = cat_idx;
            
            if (images.length > 0) {
                $('div.images-section').show();
                $('div.style-section').show();
                $('div.confirmation-section').show();
            }
            set_loading(false);
        }
	});
};

// insert an image into the WP post
function insert_image_obj(img) {
    align = $('div.style-section > fieldset > '+
        'input[name="alignment"]:checked').val();
    if (align == 'left')
        align = 'alignleft';
    else if (align == 'center')
        align = 'aligncenter';
    else if (align == 'right')
        align = 'alignright';
    else
        align = '';
    target_ = $('div.style-section > fieldset > input[name="target"]:checked').val();

    if (target_ == 'same')
        target_ = '_self';
    else
        target_ = '_blank';

    url_ = $('div.style-section > fieldset > '+
        'input[name="url"]:checked').val();
    if (url_ == 'fullsize')
        url_ = img.derivatives.xxlarge.url; // fullsize image
    else
        url_ = img.categories[0].page_url; // image page

    imurl_ = $('div.style-section > fieldset > '+
        'input[name="whatinsert"]:checked').val();
    if (imurl_ == 'fullsize')
        imurl_ = img.derivatives.xxlarge.url;
    else
        imurl_ = get_image_thumb(img);

    window.parent.tinyMCE.execCommand('mceInsertContent',
        false,
        '<a href="'+url_+'" target="'+target_+'" '+
        'class="piwigomedia-single-image">'+
            '<img src="'+imurl_+'" class="'+align+'" />'+
        '</a>'
    );
};

// insert all selected images into the WP post
function insert_selected() {
    $.each(selection, function(idx, el) {
        insert_image_obj(el);
    });
}
