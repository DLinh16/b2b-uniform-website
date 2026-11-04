<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* =====================================================
 * 1. NAP CSS CHO CHILD THEME
 * ===================================================== */
add_action( 'wp_enqueue_scripts', 'nhom5_enqueue_styles' );
function nhom5_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array( 'parent-style' ), wp_get_theme()->get( 'Version' ) );
}

/* =====================================================
 * 2. SHORTCODE [thong_bao]
 * Cach dung: [thong_bao loai="success" noidung="Noi dung"]
 * Ky thuat an toan: sanitize, validate, escape
 * ===================================================== */
add_shortcode( 'thong_bao', 'nhom5_thong_bao_shortcode' );
function nhom5_thong_bao_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'loai'    => 'info',
        'noidung' => 'Xin chao!',
    ), $atts, 'thong_bao' );

    // [1] SANITIZATION: loc du lieu dau vao
    $noidung = sanitize_text_field( $atts['noidung'] );
    $loai    = sanitize_key( $atts['loai'] );

    // [2] VALIDATION: chi chap nhan gia tri trong danh sach cho phep
    $allowed = array( 'info', 'success', 'warning' );
    if ( ! in_array( $loai, $allowed, true ) ) {
        $loai = 'info';
    }

    // [3] ESCAPING: ma hoa du lieu dau ra, chong XSS
    return '<div class="nhom5-notice nhom5-' . esc_attr( $loai ) . '">'
         . esc_html( $noidung )
         . '</div>';
}

/* =====================================================
 * 3. NUT BACK-TO-TOP (JS tu viet)
 * ===================================================== */
add_action( 'wp_footer', 'nhom5_back_to_top' );
function nhom5_back_to_top() {
    ?>
    <button id="nhom5-btt" type="button"
        aria-label="<?php echo esc_attr__( 'Lên đầu trang', 'astra-child' ); ?>">&#8593;</button>
    <script>
    (function () {
        var btn = document.getElementById('nhom5-btt');
        var body = document.body;
        var ticking = false;
        function onScroll() {
            var y = window.scrollY;
            if (btn) btn.classList.toggle('show', y > 300);
            body.classList.toggle('vu-scrolled', y > 8);
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
        }, { passive: true });
        onScroll();
        if (btn) {
            btn.addEventListener('click', function () {
                var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
            });
        }
    })();
    </script>
    <?php
}

/* =====================================================
 * 3b. HIEN DAN KHI CUON (nhe, chay mot lan)
 * Chi gan class khi trinh duyet ho tro IntersectionObserver
 * va nguoi dung khong bat "giam chuyen dong".
 * ===================================================== */
add_action( 'wp_footer', 'vu_scroll_reveal', 30 );
function vu_scroll_reveal() {
    ?>
    <script>
    (function () {
        if (!('IntersectionObserver' in window)) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        // Nhom phan tu: cac phan tu cung nhom hien lech nhau mot chut (toi da 3 buoc)
        var groups = [
            '.vh-title', '.vh-about > *', '.vh-category', '.vh-step',
            '.vh-cta .vh-container', '.vh-contact > div',
            '.va-intro > *', '.va-grid > .va-card', '.va-cta',
            '.venus-price-page > h2', '.venus-price-table', '.venus-price-note',
            '.venus-price-cta', 'ul.products > li.product', '.tv3-form'
        ];
        var seen = [];
        document.documentElement.classList.add('vu-js');

        groups.forEach(function (sel) {
            var parentIndex = new Map();
            document.querySelectorAll(sel).forEach(function (el) {
                if (seen.indexOf(el) !== -1) return;
                seen.push(el);
                var p = el.parentNode;
                var i = parentIndex.get(p) || 0;
                parentIndex.set(p, i + 1);
                el.style.setProperty('--vu-delay', (Math.min(i % 4, 3) * 80) + 'ms');
                el.classList.add('vu-reveal');
            });
        });

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    io.unobserve(e.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

        seen.forEach(function (el) { io.observe(el); });
    })();
    </script>
    <?php
}

/* =====================================================
 * 4. HARDENING: AN PHIEN BAN WORDPRESS
 * ===================================================== */
remove_action( 'wp_head', 'wp_generator' );
/* =====================================================
 * 5. WOOCOMMERCE CATALOG MODE (chi trung bay, khong ban)
 * ===================================================== */

// An nut "Them vao gio hang"
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// San pham khong the mua
add_filter( 'woocommerce_is_purchasable', '__return_false' );

// Nut "Yeu cau bao gia" tro toi trang Lien he
add_action( 'woocommerce_single_product_summary', 'nhom5_nut_bao_gia', 30 );
function nhom5_nut_bao_gia() {
    $url = esc_url( home_url( '/lien-he/' ) );
    echo '<a class="button nhom5-btn-baogia" href="' . $url . '">'
       . esc_html__( 'Yêu cầu báo giá', 'astra-child' )
       . '</a>';
}

// Gio hang, thanh toan: chuyen ve trang chu
add_action( 'template_redirect', 'nhom5_chan_cart_checkout' );
function nhom5_chan_cart_checkout() {
    if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
        wp_safe_redirect( home_url( '/' ) );
        exit;
    }
}
add_filter( 'woocommerce_get_price_html', 'nhom5_an_gia' );
function nhom5_an_gia( $price ) {
    return esc_html__( 'Liên hệ để nhận báo giá', 'astra-child' );
}

/* =====================================================
 * 6. TV2 - LOAD RIENG FILE GIAO DIEN
 * ===================================================== */
add_action( 'wp_enqueue_scripts', 'tv2_enqueue_giaodien_css', 20 );
function tv2_enqueue_giaodien_css() {
    // Font co ho tro tieng Viet day du: Lora (tieu de), Be Vietnam Pro (noi dung)
    wp_enqueue_style(
        'vu-fonts',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&family=Lora:wght@500;600&display=swap&subset=vietnamese',
        array(),
        null
    );
    wp_enqueue_style(
        'tv2-giaodien',
        get_stylesheet_directory_uri() . '/giaodien.css',
        array( 'child-style', 'vu-fonts' ),
        '1.1.0'
    );
}

add_filter( 'wp_resource_hints', 'vu_font_preconnect', 10, 2 );
function vu_font_preconnect( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
    }
    return $urls;
}

/* =====================================================
   VENUS UNIFORM - FOOTER RIÊNG
===================================================== */

function venus_uniform_custom_footer() {
    ?>
    <footer class="venus-footer">
        <div class="venus-footer-container">

            <div class="venus-footer-column">
                <h2>VENUS UNIFORM</h2>
                <p>
                    Chuyên thiết kế, sản xuất và cung cấp đồng phục
                    chuyên nghiệp cho doanh nghiệp, trường học và tổ chức.
                </p>
            </div>

            <div class="venus-footer-column">
                <h3>DANH MỤC SẢN PHẨM</h3>
                <ul>
                    <li><a href="/dongphuc/cua-hang/">Tất cả sản phẩm</a></li>
                    <li><a href="/dongphuc/cua-hang/">Đồng phục công sở</a></li>
                    <li><a href="/dongphuc/cua-hang/">Đồng phục y tế</a></li>
                    <li><a href="/dongphuc/cua-hang/">Đồng phục nhà hàng</a></li>
                </ul>
            </div>

            <div class="venus-footer-column">
                <h3>HỖ TRỢ KHÁCH HÀNG</h3>
                <ul>
                    <li><a href="/dongphuc/bang-gia/">Bảng giá</a></li>
                    <li><a href="/dongphuc/dat-lich-tu-van/">Đặt lịch tư vấn</a></li>
                    <li><a href="/dongphuc/lien-he/">Liên hệ</a></li>
                </ul>
            </div>

            <div class="venus-footer-column">
                <h3>THÔNG TIN LIÊN HỆ</h3>
                <p>Địa chỉ: Số 10 Phố Vọng, Hai Bà Trưng, Hà Nội</p>
                <p>Hotline: 0988 686 868</p>
                <p>Thời gian: Thứ 2 – Thứ 7, 8:00 – 17:30</p>
            </div>

        </div>

        <div class="venus-footer-bottom">
            © <?php echo esc_html(date('Y')); ?> VENUS UNIFORM.
            All rights reserved.
        </div>
    </footer>
    <?php
}

add_action('astra_footer', 'venus_uniform_custom_footer', 5);


/* CSS cho footer VENUS UNIFORM: da chuyen sang giaodien.css (muc 12) */

//[products limit="12" columns="3" orderby="date" order="DESC"]
/*
<style>
.venus-product-page {
    --navy: #102B46;
    --gold: #C9A45C;
    --light: #F3F5F7;
    color: #333;
    font-family: Arial, sans-serif;
    line-height: 1.7;
}
.venus-product-page * { box-sizing: border-box; }
.venus-product-page .vp-hero {
    background: linear-gradient(120deg, #102B46, #1D4668);
    color: white;
    padding: 85px 25px;
    text-align: center;
    border-radius: 6px;
    margin-bottom: 40px;
}
.venus-product-page .vp-hero .vp-label {
    color: #E3C889;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    font-size: 13px;
    margin-bottom: 10px;
}
.venus-product-page .vp-hero h1 {
    color: white;
    font-size: 42px;
    margin: 0 0 15px;
}
.venus-product-page .vp-hero p {
    max-width: 750px;
    margin: auto;
    font-size: 18px;
}
@media(max-width:768px) {
    .venus-product-page .vp-hero { padding: 60px 20px; }
    .venus-product-page .vp-hero h1 { font-size: 32px; }
}
</style>
<div class="venus-product-page">
<div class="vp-hero">
<div class="vp-label">VENUS UNIFORM</div>
<h1>Sản phẩm &amp; Giải pháp đồng phục</h1>
<p>Khám phá bộ sưu tập đồng phục chuyên nghiệp cho doanh nghiệp, trường học và tổ chức, thiết kế theo nhận diện thương hiệu của bạn.</p>
</div>
</div>
*/

add_action( 'woocommerce_archive_description', 'venus_product_page_header', 5 );

function venus_product_page_header() {
    if ( ! is_shop() ) {
        return;
    }
    ?>
    <style>
        .venus-product-page {
            --navy: #102B46;
            --gold: #C9A45C;
            --light: #F3F5F7;
            color: #333;
            line-height: 1.7;
        }

        .venus-product-page * {
            box-sizing: border-box;
        }

        .venus-product-page .vp-hero {
            background: linear-gradient(120deg, #102B46, #1D4668);
            color: white;
            padding: 85px 25px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 40px;
        }

        .venus-product-page .vp-hero .vp-label {
            color: #E3C889;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .venus-product-page .vp-hero h1 {
            color: white;
            font-size: 42px;
            margin: 0 0 15px;
        }

        .venus-product-page .vp-hero p {
            max-width: 750px;
            margin: auto;
            font-size: 18px;
        }

        @media(max-width:768px) {
            .venus-product-page .vp-hero {
                padding: 60px 20px;
            }

            .venus-product-page .vp-hero h1 {
                font-size: 32px;
            }
        }
    </style>

    <div class="venus-product-page">
        <div class="vp-hero">
            <div class="vp-label">VENUS UNIFORM</div>
            <h1>Sản phẩm &amp; Giải pháp đồng phục</h1>
            <p>
                Khám phá bộ sưu tập đồng phục chuyên nghiệp cho doanh nghiệp,
                trường học và tổ chức, thiết kế theo nhận diện thương hiệu của bạn.
            </p>
        </div>
    </div>
    <?php
}
