# GeneratePress Child — RPT Power (giangrpt)

Child theme WordPress B2B cho RPT Power (GeneratePress + WooCommerce catalog mode).

Repository này **chỉ chứa theme** — không chứa database, upload media hay plugin.

## Deploy qua cPanel Git Version Control

### Đường dẫn trên server (theo hosting hiện tại)

| Mục | Đường dẫn |
|-----|-----------|
| Home cPanel | `/home/jicnuwfm` |
| WordPress site | `/home/jicnuwfm/rpt-power.vn` |
| **Deploy theme vào** | `/home/jicnuwfm/rpt-power.vn/wp-content/themes/generatepress_child` |

### Cách 1 — Clone từ GitHub (khuyến nghị)

1. cPanel → **Git™ Version Control** → **Create**
2. Clone URL: `https://github.com/minhthangdev93/giangrpt.git`
3. Repository path: ví dụ `/home/jicnuwfm/repositories/giangrpt`
4. **Deployment** → bật deployment, đặt:
   - **Deployment path:** `/home/jicnuwfm/rpt-power.vn/wp-content/themes/generatepress_child`
5. **Deploy HEAD Commit** sau mỗi lần push lên GitHub

### Cách 2 — Push từ máy local

```bash
cd wp-content/themes/generatepress_child
git remote add origin https://github.com/minhthangdev93/giangrpt.git
git push -u origin main
```

Sau đó trên cPanel: **Pull or Deploy** → **Deploy HEAD Commit**.

## Lưu ý sau khi deploy

- **Dữ liệu admin (ACF, sản phẩm, trang)** nằm trong **database** — deploy theme **không xóa** nội dung đã nhập.
- Cần cài sẵn trên server: **GeneratePress** (parent), **WooCommerce**, **ACF** (nếu dùng).
- Parent theme `generatepress` **không** nằm trong repo này — cài qua WordPress/cPanel như bình thường.
- Sau deploy: **LiteSpeed / cache** → Purge All; trình duyệt **Ctrl+F5**.

## Cấu trúc chính

```
assets/          CSS, JS, fonts
inc/             PHP modules (WooCommerce, video, home, …)
template-parts/  Template parts
page-templates/  Page templates
woocommerce/     WooCommerce overrides
```
