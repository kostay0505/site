<?php
/**
 * ad-editor.php
 * Форма создания и редактирования объявления в ЛК
 * Вызывается из page-account.php при section=ads & mode=new|edit
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

$account_page_url = get_query_var( 'account_page_url' );
$mode             = sanitize_text_field( wp_unslash( $_GET['mode'] ?? '' ) );
$post_id          = ( 'edit' === $mode && ! empty( $_GET['ad_id'] ) ) ? intval( $_GET['ad_id'] ) : 0;
$is_edit          = (bool) $post_id;
$post             = $is_edit ? get_post( $post_id ) : null;

// Подготовка значений полей
$title       = $is_edit ? $post->post_title : '';
$desc        = $is_edit ? $post->post_content : '';
$price       = $is_edit ? get_post_meta( $post_id, '_price', true ) : '';
$currency    = $is_edit ? get_post_meta( $post_id, '_currency', true ) : '';
$cond        = $is_edit ? get_post_meta( $post_id, '_condition', true ) : 'new';
$cat_terms   = $is_edit
  ? wp_get_post_terms( $post_id, 'product_cat', [ 'fields' => 'ids' ] )
  : [];
$cat_id      = ! empty( $cat_terms ) ? $cat_terms[0] : 0;

// Вычисляем выбранную родительскую категорию
$parent_cat_id = 0;
if ( $cat_id ) {
    $term = get_term( $cat_id, 'product_cat' );
    if ( $term && ! is_wp_error( $term ) ) {
        $parent_cat_id = $term->parent ? $term->parent : $term->term_id;
    }
}

// Списки категорий
$parent_cats = get_terms([
    'taxonomy'   => 'product_cat',
    'parent'     => 0,
    'hide_empty' => false,
]);
$subcats = $parent_cat_id ? get_terms([
    'taxonomy'   => 'product_cat',
    'parent'     => $parent_cat_id,
    'hide_empty' => false,
]) : [];
$gallery_ids = $is_edit
  ? array_filter( array_map( 'absint',
      explode( ',', get_post_meta( $post_id, '_product_image_gallery', true ) )
    ) )
  : [];
$thumb_id    = $is_edit ? get_post_thumbnail_id( $post_id ) : 0;
$quantity    = $is_edit ? get_post_meta( $post_id, '_quantity', true ) : '';
$unit        = $is_edit ? get_post_meta( $post_id, '_unit', true ) : '';
$country     = $is_edit ? get_post_meta( $post_id, '_country', true ) : '';
$city        = $is_edit ? get_post_meta( $post_id, '_city', true ) : '';
?>

<h2 class="ad-editor-title">
  <?php echo $is_edit
    ? esc_html__( 'Редактировать объявление', 'my-custom-theme' )
    : esc_html__( 'Создание объявления',    'my-custom-theme' ); ?>
</h2>

<form
  method="post"
  action="<?php echo esc_url( add_query_arg( [ 'section'=>'ads','mode'=>$mode,'ad_id'=>$post_id ], $account_page_url ) ); ?>"
  enctype="multipart/form-data"
  class="ad-form"
>
  <?php wp_nonce_field( 'mytheme_save_ad','mytheme_ad_nonce' ); ?>
  <div id="remove-images-container"></div>
  <input type="hidden" name="account_url" value="<?php echo esc_attr( $account_page_url ); ?>">
  <?php if ( $is_edit ) : ?>
    <input type="hidden" name="ad_id" value="<?php echo esc_attr( $post_id ); ?>">
  <?php endif; ?>

  <div class="ad-form__inner">

    <!-- Название -->
    <div class="form-row">
      <label for="ad_title"><?php esc_html_e( 'Название объявления','my-custom-theme' ); ?></label>
      <input type="text" id="ad_title" name="ad_title"
             value="<?php echo esc_attr( $title ); ?>" required class="form-control">
    </div>

    <!-- Категория -->
    <div class="form-row">
      <label for="ad_parent_cat"><?php esc_html_e( 'Категория', 'my-custom-theme' ); ?></label>
      <select name="ad_parent_cat" id="ad_parent_cat" class="form-control">
        <option value=""><?php esc_html_e( 'Выберите…', 'my-custom-theme' ); ?></option>
        <?php foreach ( $parent_cats as $p ) : ?>
          <option value="<?php echo esc_attr( $p->term_id ); ?>" <?php selected( $parent_cat_id, $p->term_id ); ?>><?php echo esc_html( $p->name ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Подкатегория -->
    <div class="form-row" id="subcat-row" style="<?php echo $parent_cat_id ? '' : 'display:none'; ?>">
      <label for="ad_cat"><?php esc_html_e( 'Подкатегория', 'my-custom-theme' ); ?></label>
      <select name="ad_cat" id="ad_cat" class="form-control" data-selected="<?php echo esc_attr( $cat_id ); ?>">
        <option value=""><?php esc_html_e( 'Выберите…', 'my-custom-theme' ); ?></option>
        <?php foreach ( $subcats as $sub ) : ?>
          <option value="<?php echo esc_attr( $sub->term_id ); ?>" <?php selected( $cat_id, $sub->term_id ); ?>><?php echo esc_html( $sub->name ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Модель -->
    <div class="form-row">
      <label for="ad_model"><?php esc_html_e( 'Модель','my-custom-theme' ); ?></label>
      <select name="ad_model" id="ad_model" class="form-control">
        <option value=""><?php esc_html_e( 'Выберите…','my-custom-theme' ); ?></option>
        <!-- TODO: опции -->
      </select>
    </div>

    <!-- Состояние -->
    <div class="form-row">
      <label><?php esc_html_e( 'Состояние','my-custom-theme' ); ?></label><br>
      <label><input type="radio" name="ad_cond" value="new" <?php checked($cond,'new');?>>
        <?php esc_html_e('Новое','my-custom-theme');?></label>
      <label style="margin-left:1em;"><input type="radio" name="ad_cond" value="used" <?php checked($cond,'used');?>>
        <?php esc_html_e('Б/у','my-custom-theme');?></label>
    </div>

    <!-- Количество и единица -->
    <div class="form-row form-row--2">
      <div>
        <label for="ad_quantity"><?php esc_html_e( 'Количество','my-custom-theme' ); ?></label>
        <input type="number" id="ad_quantity" name="ad_quantity"
               value="<?php echo esc_attr($quantity);?>" class="form-control">
      </div>
      <div>
        <label for="ad_unit"><?php esc_html_e( 'Ед.','my-custom-theme' ); ?></label>
        <select name="ad_unit" id="ad_unit" class="form-control">
          <option value="шт" <?php selected($unit,'шт');?>>шт</option>
          <option value="комп" <?php selected($unit,'комп');?>>комп</option>
        </select>
      </div>
    </div>

    <!-- Цена и валюта -->
    <div class="form-row form-row--2">
      <div>
        <label for="ad_price"><?php esc_html_e( 'Цена','my-custom-theme' ); ?></label>
        <input type="number" id="ad_price" name="ad_price"
               value="<?php echo esc_attr($price);?>" class="form-control" step="0.01" min="0">
      </div>
      <div>
        <label for="ad_currency"><?php esc_html_e( 'Валюта','my-custom-theme' ); ?></label>
        <select name="ad_currency" id="ad_currency" class="form-control">
          <option value="₽" <?php selected($currency,'₽');?>>₽</option>
          <option value="$" <?php selected($currency,'$');?>>$</option>
          <option value="€" <?php selected($currency,'€');?>>€</option>
        </select>
      </div>
    </div>

    <!-- Описание -->
    <div class="form-row">
      <label for="ad_desc"><?php esc_html_e( 'Описание','my-custom-theme' ); ?></label>
      <textarea id="ad_desc" name="ad_desc" rows="4" class="form-control"><?php echo esc_textarea($desc);?></textarea>
    </div>

    <!-- Загрузка фото -->
    <div class="form-row">
      <label><?php esc_html_e('Фотографии','my-custom-theme');?></label>
      <div class="file-upload-wrap">
        <label for="ad-images-input" class="btn-upload">
          <span class="icon">📷</span><?php esc_html_e('Добавить фото','my-custom-theme');?>
        </label>
        <input type="file" id="ad-images-input" name="ad_images[]" accept="image/*" multiple class="form-control">
      </div>
    </div>

    <!-- Превью изображений -->
    <div class="form-row">
      <div class="image-previews">
        <?php if($is_edit):
          $all = $thumb_id ? array_merge([$thumb_id],$gallery_ids):$gallery_ids;
          foreach($all as $gid): ?>
            <div class="image-thumb" data-id="<?php echo $gid;?>">
              <?php echo wp_get_attachment_image($gid,'thumbnail');?>
              <button type="button" class="remove-existing" title="<?php esc_attr_e('Удалить','my-custom-theme');?>">×</button>
            </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- Страна и город -->
    <div class="form-row form-row--2">
      <div>
        <label for="ad_country"><?php esc_html_e('Страна','my-custom-theme');?></label>
        <select name="ad_country" id="ad_country" class="form-control">
          <option value=""><?php esc_html_e('Выберите…','my-custom-theme');?></option>
        </select>
      </div>
      <div>
        <label for="ad_city"><?php esc_html_e('Город','my-custom-theme');?></label>
        <select name="ad_city" id="ad_city" class="form-control">
          <option value=""><?php esc_html_e('Выберите…','my-custom-theme');?></option>
        </select>
      </div>
    </div>

    <!-- Действия -->
    <div class="form-row ad-actions-full">
      <button type="submit" class="btn btn-submit"><?php esc_html_e('Сохранить','my-custom-theme');?></button>
      <?php if($is_edit):?>
        <button type="submit" name="ad_delete" value="1" class="btn btn-secondary ad-delete-btn"><?php esc_html_e('Удалить','my-custom-theme');?></button>
      <?php endif;?>
    </div>

  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const ajaxUrl     = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
  const parentSel   = document.getElementById('ad_parent_cat');
  const subSel      = document.getElementById('ad_cat');
  const subRow      = document.getElementById('subcat-row');
  const fileInput   = document.getElementById('ad-images-input');
  const previews    = document.querySelector('.image-previews');
  const removeBox   = document.getElementById('remove-images-container');

  if (parentSel && subSel) {
    const loadSubcats = function(pid, selected){
      if (!pid) {
        subSel.innerHTML = '<option value=""><?php esc_html_e( 'Выберите…', 'my-custom-theme' ); ?></option>';
        subSel.disabled = true;
        if (subRow) subRow.style.display = 'none';
        return;
      }
      fetch(ajaxUrl, {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
        body: 'action=mytheme_get_subcategories&parent_id=' + pid
      })
      .then(r => r.json())
      .then(res => {
        subSel.innerHTML = '<option value=""><?php esc_html_e( 'Выберите…', 'my-custom-theme' ); ?></option>';
        if (res.success && Array.isArray(res.data) && res.data.length) {
          res.data.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.name;
            if (selected && String(selected) === String(t.id)) opt.selected = true;
            subSel.appendChild(opt);
          });
          subSel.disabled = false;
          if (subRow) subRow.style.display = '';
        } else {
          subSel.disabled = true;
          if (subRow) subRow.style.display = 'none';
        }
      });
    };

    parentSel.addEventListener('change', function(){
      loadSubcats(this.value, null);
    });

    if (parentSel.value) {
      loadSubcats(parentSel.value, subSel.dataset.selected);
    }
  }

  if(!fileInput||!previews||!removeBox) return;

  const dt = new DataTransfer();

  fileInput.addEventListener('change', ()=>{  
    Array.from(fileInput.files).forEach(file=>{
      dt.items.add(file);
      const reader=new FileReader();
      reader.onload=e=>{
        const div=document.createElement('div');
        div.classList.add('image-thumb','new-thumb');
        div.dataset.id='new-'+Date.now()+'-'+Math.random().toString(36).substr(2,5);
        div.innerHTML=`<img src="${e.target.result}"><button type="button" class="remove-existing">×</button>`;
        previews.append(div);
      };
      reader.readAsDataURL(file);
    });
    fileInput.files=dt.files;
  });

  previews.addEventListener('click', e=>{
    if(!e.target.closest('button.remove-existing')) return;
    const thumb=e.target.closest('.image-thumb');
    const id=thumb.dataset.id;
    if(!id.startsWith('new-')){
      const inp=document.createElement('input');
      inp.type='hidden'; inp.name='remove_images[]'; inp.value=id;
      removeBox.append(inp);
    } else {
      // rebuild dt without this
      const newThumbs=[...previews.querySelectorAll('.image-thumb.new-thumb')];
      const idx=newThumbs.indexOf(thumb);
      const newDT=new DataTransfer();
      Array.from(dt.files).forEach((f,i)=>{ if(i!==idx) newDT.items.add(f); });
      dt.items=newDT.items;
      fileInput.files=dt.files;
    }
    thumb.remove();
  });
});
</script>
