# Báo cáo thực hành - TV4 (Nguyên): Đặt lịch tư vấn B2B

- **Nhiệm vụ:** Cấu hình plugin Bookly, tạo trang "Đặt lịch tư vấn".
- **Chi tiết thực hiện:**
  - Xóa toàn bộ dữ liệu mẫu (demo data) tiếng Anh của Bookly.
  - Thiết lập thành công 5 danh mục & dịch vụ đồng phục B2B:
    1. Đồng phục Công sở
    2. Đồng phục Y tế & Spa
    3. Đồng phục Áo thun & Sự kiện
    4. Đồng phục Bảo hộ Lao động
    5. Đồng phục Nhà hàng - Khách sạn
  - Gắn shortcode `[bookly-form]` vào trang Đặt lịch tư vấn ngoài Front-end.
- **Minh chứng:** Xem trong thư mục `screenshots/` bên dưới.

---

## 🛠️ Hướng dẫn tích hợp nhanh (Sử dụng File Database có sẵn)

Toàn bộ dữ liệu cấu hình Bookly (5 danh mục dịch vụ B2B, lịch làm việc, trang đặt lịch) đã được tích hợp sẵn trong file database của dự án (`database/wp_dongphuc.sql`). 

Để đồng bộ dữ liệu vào máy local của bạn, chỉ cần làm theo 2 bước đơn giản sau:

### Bước 1: Kích hoạt Plugin Bookly
1. Vào Quản trị WordPress (**Dashboard**) > **Plugins** > **Add New** (Thêm mới).
2. Tìm kiếm từ khóa `Bookly`.
3. Bấm **Install Now** (Cài đặt) và sau đó bấm **Activate** (Kích hoạt).

### Bước 2: Nhập (Import) Cơ sở dữ liệu của dự án
1. Truy cập **phpMyAdmin** trên máy tính của bạn (`http://localhost/phpmyadmin`).
2. Chọn cơ sở dữ liệu WordPress của dự án (`wp_dongphuc`).
3. Chọn thẻ **Import** (Nhập).
4. Bấm **Choose File** và chọn file `database/wp_dongphuc.sql` nằm trong thư mục nguồn của dự án.
5. Bấm **Import** (hoặc *Go*) ở cuối trang để hoàn tất.

👉 **Kết quả:** Sau khi import xong, toàn bộ 5 danh mục dịch vụ B2B và trang *Đặt lịch tư vấn* sẽ tự động hiển thị đầy đủ trên website của bạn mà không cần phải chỉnh sửa hay thiết lập lại thủ công!