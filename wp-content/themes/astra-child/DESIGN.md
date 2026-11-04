# VENUS UNIFORM — Hệ thống giao diện

Mọi trang dùng chung một bộ token, khai báo trong `giaodien.css` (mục 1). Khi thêm trang mới, dùng lại token này, không tự chọn màu/font riêng.

## Ý tưởng

Xưởng may đồng phục cho doanh nghiệp: vải suiting màu navy, khuy đồng, **đường chỉ may**.
Đường chỉ (nét đứt màu đồng) là chi tiết nhận diện duy nhất. Nó xuất hiện ở viền trong hero, gạch dưới tiêu đề section, menu đang chọn, và chỗ nối giữa các bước quy trình. Không thêm chi tiết trang trí nào khác.

## Màu

| Token | Giá trị | Vai trò |
|---|---|---|
| `--vu-navy` | `#102B46` | Màu chủ đạo: tiêu đề, header bảng, nút phụ |
| `--vu-navy-deep` | `#0B1F33` | Footer, khối CTA |
| `--vu-brass` | `#C9A45C` | Nút chính, đường chỉ |
| `--vu-brass-dark` | `#A9823F` | Số thứ tự, nhãn nhỏ trên nền sáng |
| `--vu-brass-soft` | `#E3C889` | Chữ nhấn trên nền tối, hover của nút chính |
| `--vu-shirting` | `#F3F5F7` | Nền phụ |
| `--vu-ink` / `--vu-muted` | `#24303D` / `#5B6672` | Chữ thân bài / chữ phụ |

Nút chính dùng **chữ navy trên nền đồng**. Chữ trắng trên `#C9A45C` chỉ đạt 2.3:1, không đủ tương phản.

## Chữ

- Tiêu đề: **Lora** 500/600
- Nội dung: **Be Vietnam Pro** 400/500/600

Cả hai font đều hỗ trợ đầy đủ dấu tiếng Việt và được nạp từ Google Fonts trong `functions.php`.

## Chuyển động

Chuyển động giữ ở mức vừa phải, mỗi loại chỉ dùng một kiểu:

- Hero hiện dần khi tải trang (700ms, lệch 80ms).
- Khối nội dung hiện dần khi cuộn tới, mỗi khối một lần, lệch tối đa 3 bước.
- Hover: ảnh phóng 4%, viền thẻ đậm hơn, đường chỉ chạy dưới thẻ, nút nhích lên 1px.
- Header có bóng nhẹ sau khi cuộn.
- `prefers-reduced-motion` tắt toàn bộ chuyển động.

## Lưu ý kỹ thuật

CSS riêng của từng trang nằm trong post content (database) và được in **sau** `giaodien.css`. Vì vậy selector trong `giaodien.css` có thêm tiền tố `body` để thắng độ ưu tiên. Không cần sửa database.
