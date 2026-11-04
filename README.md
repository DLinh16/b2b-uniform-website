# Báo cáo thực hành - TV2 (Thanh): Thiết kế Giao diện & Bố cục Website B2B Venus Uniform

- **Nhiệm vụ:** Xây dựng, cấu hình giao diện và bố cục chi tiết cho các trang cốt lõi của website thương mại điện tử B2B (Trang chủ, Giới thiệu, Cửa hàng, Liên hệ, Tài khoản, Footer) trên nền tảng WordPress & WooCommerce, sử dụng Astra Theme và tuân thủ bộ nhận diện thương hiệu cao cấp.
- **Chi tiết thực hiện:**
  - Tùy chỉnh và ẩn toàn bộ tiêu đề/header thô mặc định của theme (`header.entry-header`, `h1.entry-title`) để đồng bộ hóa giao diện custom HTML/CSS toàn trang[cite: 1].
  - Thiết kế Trang Chủ & Trang Cửa hàng (`/san-pham`)[cite: 1]:
    - Xây dựng banner chào mừng đẳng cấp với tông màu tối (`#0f172a`) kết hợp điểm nhấn vàng gold (`#b78628`)[cite: 1].
    - Thiết kế lưới sản phẩm (Product Grid) chuyên nghiệp, trực quan[cite: 1].
  - Thiết lập thành công 5 danh mục & sản phẩm đồng phục B2B chủ lực[cite: 1]:
    1. Đồng phục Công sở (Vest, Sơ mi chống nhăn)[cite: 1]
    2. Đồng phục Y tế & Spa (Blouse, đồ thẩm mỹ kháng khuẩn)[cite: 1]
    3. Đồng phục Áo thun & Sự kiện (Áo polo cotton co giãn 4 chiều)[cite: 1]
    4. Đồng phục Bảo hộ Lao động (Quần áo kỹ sư, dải phản quang an toàn)[cite: 1]
    5. Đồng phục Nhà hàng - Khách sạn (Đồ bếp, tạp dề, lễ tân F&B)[cite: 1]
  - Tích hợp các khối cam kết chất lượng, thiết kế 3D miễn phí, may mẫu thực tế và chính sách bảo hành 6 tháng[cite: 1].
  - Thiết kế Trang Liên hệ (`/lien-he`) với bố cục 2 cột cân đối: Cột trái cung cấp thông tin trụ sở, hotline, giờ làm việc dạng thẻ nổi bật; Cột phải tích hợp form gửi yêu cầu tư vấn nhanh chuẩn B2B[cite: 1].
  - Tùy chỉnh Trang Tài khoản Khách hàng (`/tai-khoan`): Đồng bộ hóa giao diện bảng điều khiển WooCommerce (My Account), thiết kế lại menu điều hướng bên trái và khung nội dung trực quan, sang trọng[cite: 1].
  - Thiết kế hệ thống Footer (Chân trang) đồng bộ gồm 4 cột: Thông tin thương hiệu Venus Uniform, Liên kết nhanh, 5 danh mục sản phẩm chủ lực và Trụ sở chính[cite: 1].
- **Minh chứng:** Xem chi tiết hình ảnh bố cục trong thư mục `screenshots/` và file cấu trúc XML tại `docs/tv2-thanh/`[cite: 1].

---

## Hướng dẫn tích hợp nhanh (Sử dụng File Database có sẵn)

Toàn bộ dữ liệu cấu hình giao diện, cấu trúc trang (Trang chủ, Giới thiệu, Cửa hàng, Liên hệ, Tài khoản) và hệ thống footer đã được tích hợp sẵn trong file database của dự án (`database/wp_dongphuc.sql`)[cite: 8]. 

Để đồng bộ dữ liệu vào máy local của bạn, chỉ cần làm theo 2 bước đơn giản sau:

### Bước 1: Kích hoạt Child Theme (Astra Child - Nhom 5)
1. Vào Quản trị WordPress (**Dashboard**) > **Appearance** (Giao diện) > **Themes** (Giao diện)[cite: 8].
2. Tìm và kích hoạt theme **Astra Child - Nhom 5** (đã bao gồm mã nguồn custom HTML/CSS của nhóm)[cite: 8].

### Bước 2: Nhập (Import) Cơ sở dữ liệu của dự án
1. Truy cập **phpMyAdmin** trên máy tính của bạn (`http://localhost/phpmyadmin`)[cite: 8].
2. Chọn cơ sở dữ liệu WordPress của dự án (`wp_dongphuc`)[cite: 8].
3. Chọn thẻ **Import** (Nhập)[cite: 8].
4. Bấm **Choose File** và chọn file `database/wp_dongphuc.sql` nằm trong thư mục nguồn của dự án[cite: 8].
5. Bấm **Import** (hoặc *Go*) ở cuối trang để hoàn tất[cite: 8].

> File SQL có sẵn lệnh `DROP TABLE IF EXISTS`, nên có thể import đè lên database cũ. Site URL trong database là `http://localhost/dongphuc`.

### Bước 3: Chép ảnh sản phẩm (bắt buộc từ bản 16 sản phẩm)
Thư mục `uploads` không nằm trong repo, nên ảnh sản phẩm (đã cắt sẵn tỉ lệ 4:5) được để riêng trong `database/anh-san-pham-4x5/`.
Chép **toàn bộ** thư mục này thành:

```
C:\xampp\htdocs\dongphuc\wp-content\uploads\vu-sanpham\
```

Nếu không chép, ảnh sản phẩm ở trang Cửa hàng sẽ không hiển thị. Cách tạo lại ảnh và sản phẩm từ đầu xem trong `tools/` và `wp-content/themes/astra-child/DESIGN.md`.

**Kết quả:** Sau khi import xong, toàn bộ giao diện các trang cốt lõi, bố cục 5 danh mục B2B và hệ thống footer của Thành viên 2 sẽ hiển thị hoàn chỉnh trên website của bạn[cite: 1, 8].
