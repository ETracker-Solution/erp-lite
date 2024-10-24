<div class="ddwcpos-products-tab-wrapper">
    <div class="ddwcpos-category-wrapper">
        <h2>Select Category</h2>
        <div class="ddwcpos-categories-container">
            <a class="ddwcpos-category-card" :class="selected_category == '' ? 'ddwcpos-category-active' : ''"
               href="#" @click="clickedOnCategory('')">
                    <span role="img" aria-label="database" class="anticon anticon-database">
                        <svg viewBox="64 64 896 896" focusable="false" data-icon="database" width="1em" height="1em"
                             fill="currentColor" aria-hidden="true">
                            <path
                                d="M832 64H192c-17.7 0-32 14.3-32 32v832c0 17.7 14.3 32 32 32h640c17.7 0 32-14.3 32-32V96c0-17.7-14.3-32-32-32zm-600 72h560v208H232V136zm560 480H232V408h560v208zm0 272H232V680h560v208zM304 240a40 40 0 1080 0 40 40 0 10-80 0zm0 272a40 40 0 1080 0 40 40 0 10-80 0zm0 272a40 40 0 1080 0 40 40 0 10-80 0z"></path></svg></span>
                <p>All</p>
            </a>
            <a class="ddwcpos-category-card"  :class="selected_category == category.id ? 'ddwcpos-category-active' : ''"
               href="#" v-for="(category, index) in categories" v-bind:value="category.id" @click="clickedOnCategory(category.id)">
                <img
                    src="https://demo.devdiggers.com/multipos-point-of-sale-for-woocommerce/wp-content/uploads/2021/12/clothing-category.png"
                    :alt="category.name" width="24" height="24">
                <p :title="category.name">@{{ category.name }}</p>
            </a>
        </div>
    </div>
    <div class="ddwcpos-search-wrapper">
        <h2>Products</h2>
        <div class="ddwcpos-search-input-wrapper" style="grid-template-columns: 90% max-content max-content auto;">
            <div class="ddwcpos-search-input">
                    <span role="img" aria-label="search" class="anticon anticon-search">
                        <svg viewBox="64 64 896 896" focusable="false" data-icon="search" width="1em" height="1em"
                             fill="currentColor" aria-hidden="true">
                            <path
                                d="M909.6 854.5L649.9 594.8C690.2 542.7 712 479 712 412c0-80.2-31.3-155.4-87.9-212.1-56.6-56.7-132-87.9-212.1-87.9s-155.5 31.3-212.1 87.9C143.2 256.5 112 331.8 112 412c0 80.1 31.3 155.5 87.9 212.1C256.5 680.8 331.8 712 412 712c67 0 130.6-21.8 182.7-62l259.7 259.6a8.2 8.2 0 0011.6 0l43.6-43.5a8.2 8.2 0 000-11.6zM570.4 570.4C528 612.7 471.8 636 412 636s-116-23.3-158.4-65.6C211.3 528 188 471.8 188 412s23.3-116.1 65.6-158.4C296 211.3 352.2 188 412 188s116.1 23.2 158.4 65.6S636 352.2 636 412s-23.3 116.1-65.6 158.4z"></path>
                        </svg>
                    </span>
                <input type="text" class="ddwcpos-form-control"
                       placeholder="Search Product by title, ID, SKU or Barcode Number" autocomplete="off" v-model="search_string" @keypress="getProductBySearchString()">
            </div>
{{--            <div class="ddwcpos-icon-card ddwcpos-barcode-icon" title="Add Product via Barcode">--}}
{{--                    <span role="img" aria-label="barcode" class="anticon anticon-barcode">--}}
{{--                        <svg viewBox="64 64 896 896" focusable="false" data-icon="barcode" width="1em" height="1em"--}}
{{--                             fill="currentColor" aria-hidden="true">--}}
{{--                            <path--}}
{{--                                d="M120 160H72c-4.4 0-8 3.6-8 8v688c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8V168c0-4.4-3.6-8-8-8zm833 0h-48c-4.4 0-8 3.6-8 8v688c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8V168c0-4.4-3.6-8-8-8zM200 736h112c4.4 0 8-3.6 8-8V168c0-4.4-3.6-8-8-8H200c-4.4 0-8 3.6-8 8v560c0 4.4 3.6 8 8 8zm321 0h48c4.4 0 8-3.6 8-8V168c0-4.4-3.6-8-8-8h-48c-4.4 0-8 3.6-8 8v560c0 4.4 3.6 8 8 8zm126 0h178c4.4 0 8-3.6 8-8V168c0-4.4-3.6-8-8-8H647c-4.4 0-8 3.6-8 8v560c0 4.4 3.6 8 8 8zm-255 0h48c4.4 0 8-3.6 8-8V168c0-4.4-3.6-8-8-8h-48c-4.4 0-8 3.6-8 8v560c0 4.4 3.6 8 8 8zm-79 64H201c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h112c4.4 0 8-3.6 8-8v-48c0-4.4-3.6-8-8-8zm257 0h-48c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8v-48c0-4.4-3.6-8-8-8zm256 0H648c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h178c4.4 0 8-3.6 8-8v-48c0-4.4-3.6-8-8-8zm-385 0h-48c-4.4 0-8 3.6-8 8v48c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8v-48c0-4.4-3.6-8-8-8z"></path>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--            </div>--}}
{{--            <div class="ddwcpos-icon-card" title="Add Custom Product">--}}
{{--                    <span role="img" aria-label="plus" class="anticon anticon-plus">--}}
{{--                        <svg viewBox="64 64 896 896" focusable="false" data-icon="plus" width="1em" height="1em"--}}
{{--                             fill="currentColor" aria-hidden="true">--}}
{{--                            <defs></defs>--}}
{{--                            <path d="M482 152h60q8 0 8 8v704q0 8-8 8h-60q-8 0-8-8V160q0-8 8-8z"></path>--}}
{{--                            <path d="M176 474h672q8 0 8 8v60q0 8-8 8H176q-8 0-8-8v-60q0-8 8-8z"></path>--}}
{{--                        </svg>--}}
{{--                    </span>--}}
{{--            </div>--}}
            <span>@{{ products.length }} Results</span>
        </div>
    </div>
    <div class="ddwcpos-grid ddwcpos-products-list" data-lazyload-listened="35"
         style="height: calc(-218px + 100vh); width: 1107px; overflow: auto; will-change: transform; direction: ltr;">
        <div>
            <div class="row" style="height: 1855px; width: 920.656px; margin-right: 0">
                <div v-for="(row, index) in products" class="col-md-2">
                    <div class=""
                         style="height: 265px; width: max-content" v-on:click="selectProductToSell(row)">
                        <div class="ddwcpos-product-card ddwcpos-product-image-top">
                            <div class="ddwcpos-product-thumbnail">
                                <img width="150" height="150"
                                     src="https://demo.devdiggers.com/multipos-point-of-sale-for-woocommerce/wp-content/uploads/2021/08/album-1-150x150.jpg"
                                     class="attachment-thumbnail size-thumbnail"
                                     alt="" decoding="async" loading="lazy"
                                     sizes="(max-width: 150px) 100vw, 150px">
                            </div>
                            <div class="ddwcpos-product-details">
                                <h2 title="Album">@{{ row.name }}</h2>
                                <p>
                                <span class="woocommerce-Price-amount amount">
                                    <bdi><span class="woocommerce-Price-currencySymbol">TK.</span>@{{ row.price }}</bdi>
                                </span><br>
                                    <mark class="instock" v-if="row.stock > 0">In Stock( @{{ row.stock }})</mark>
                                    <mark class="instock" style="color: darkred" v-else>Out of Stock</mark>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
