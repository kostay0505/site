<?php
get_header();

// 1. Название текущей подкатегории
$term = get_queried_object(); 
?>
<div class="subcategory-header">
  <h1 class="subcategory-title"><?php echo esc_html( $term->name ); ?></h1>
  <form class="subcategory-filter" method="get">
    <div class="filter-row">
      <label>
        Sort by
        <select name="orderby">
          <option value="date" <?php selected( $_GET['orderby'] ?? '', 'date' ); ?>>Date Descending</option>
          <option value="title" <?php selected( $_GET['orderby'] ?? '', 'title' ); ?>>Title</option>
          <!-- и т.д. -->
        </select>
      </label>
      <label>
        Ads per Page
        <select name="per_page">
          <?php foreach ([10,20,40,80] as $n): ?>
            <option value="<?php echo $n; ?>" <?php selected( $_GET['per_page'] ?? '', $n ); ?>>
              <?php echo $n; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <div class="filter-row">
      <label>
        ZIP
        <input type="text" name="zip" value="<?php echo esc_attr( $_GET['zip'] ?? '' ); ?>" placeholder="ZIP">
      </label>
      <label>
        Radius
        <select name="radius">
          <?php foreach (['5 km','10 km','20 km','50 km'] as $r): ?>
            <option value="<?php echo $r; ?>" <?php selected( $_GET['radius'] ?? '', $r ); ?>>
              <?php echo $r; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
    </div>
    <label class="filter-checkbox">
      <input type="checkbox" name="used_only" <?php checked( isset($_GET['used_only']) ); ?>>
      Show only used items
    </label>
    <button type="submit" class="button">Filter</button>
  </form>
</div>
