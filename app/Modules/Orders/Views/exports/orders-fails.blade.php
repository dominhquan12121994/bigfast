<table border="1">
    <tr>
        <td colspan="17" style="text-align: center; height: 60px">
            <b>Danh sách đơn hàng</b><br>
            (*) Thông tin bắt buộc phải điền
        </td>
    </tr>
    <tr>
        <td colspan="4" style="text-align: center; height: 40px; background: orange">
            <b>Thông tin người nhận</b>
        </td>
        <td colspan="13" style="text-align: center; height: 40px; background: #0d86ff">
            <b>Thông tin đơn hàng</b>
        </td>
    </tr>
    <tr>
        <td style="width: 25px"><b>Tên người nhận *</b></td>
        <td style="width: 20px"><b>Số điện thoại *</b></td>
        <td style="width: 45px"><b>Số nhà/ngõ/ngách/hẻm, Đường/Phố *</b></td>
        <td style="width: 60px"><b>Phường/xã, Quận/huyện, Tỉnh/thành *</b></td>
        <td style="width: 30px"><b>Gói cước *</b><br>
            1 = Giao hàng nhanh<br>
            2 = Giao hàng siêu tốc<br>
            3 = Giao hàng vũ trụ</td>
        <td style="width: 20px"><b>Tiền thu hộ *</b><br>
            Nếu không có thu hộ thì nhập 0</td>
        <td style="width: 30px"><b>Yêu cầu đơn hàng *</b><br>
            1 = Cho thử hàng<br>
            2 = Cho xem không thử<br>
            3 = Không cho xem</td>
        <td style="width: 20px"><b>Khối lượng (Gram) *</b><br>
            Tối đa 100,000 gram</td>
        <td style="width: 16px"><b>Dài (cm)</b></td>
        <td style="width: 16px"><b>Rộng (cm)</b></td>
        <td style="width: 16px"><b>Cao (cm)</b></td>
        <td style="width: 20px"><b>Giá trị hàng hoá</b></td>
        <td style="width: 16px"><b>Shop trả ship</b></td>
        <td style="width: 16px"><b>Mã đơn hàng riêng</b></td>
        <td style="width: 20px"><b>Tên hàng hoá *</b></td>
        <td style="width: 16px"><b>Số lượng</b></td>
        <td style="width: 25px"><b>Ghi chú thêm</b></td>
        <td style="width: 16px">ID Store</td>
        <td style="width: 16px">ID Shop</td>
    </tr>
    @foreach($orders as $stt => $order)
        <tr>
            <td style="{{ (substr($order['ten_nguoi_nhan'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['ten_nguoi_nhan'], 0, 6) === 'error-') ? substr($order['ten_nguoi_nhan'], 6) : $order['ten_nguoi_nhan'] }}
            </td>
            <td style="{{ (substr($order['so_dien_thoai'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['so_dien_thoai'], 0, 6) === 'error-') ? substr($order['so_dien_thoai'], 6) : $order['so_dien_thoai'] }}
            </td>
            <td style="{{ (substr($order['so_nhangongachhem_duongpho'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['so_nhangongachhem_duongpho'], 0, 6) === 'error-') ? substr($order['so_nhangongachhem_duongpho'], 6) : $order['so_nhangongachhem_duongpho'] }}
            </td>
            <td style="{{ (substr($order['phuongxa_quanhuyen_tinhthanh'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['phuongxa_quanhuyen_tinhthanh'], 0, 6) === 'error-') ? substr($order['phuongxa_quanhuyen_tinhthanh'], 6) : $order['phuongxa_quanhuyen_tinhthanh'] }}
            </td>
            <td style="{{ (substr($order['goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru'], 0, 6) === 'error-') ? substr($order['goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru'], 6) : $order['goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru'] }}
            </td>
            <td style="{{ (substr($order['tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0'], 0, 6) === 'error-') ? substr($order['tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0'], 6) : $order['tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0'] }}
            </td>
            <td style="{{ (substr($order['yeu_cau_don_hang_1_cho_thu_hang_2_cho_xem_khong_thu_3_khong_cho_xem'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['yeu_cau_don_hang_1_cho_thu_hang_2_cho_xem_khong_thu_3_khong_cho_xem'], 0, 6) === 'error-') ? substr($order['yeu_cau_don_hang_1_cho_thu_hang_2_cho_xem_khong_thu_3_khong_cho_xem'], 6) : $order['yeu_cau_don_hang_1_cho_thu_hang_2_cho_xem_khong_thu_3_khong_cho_xem'] }}
            </td>
            <td style="{{ (substr($order['khoi_luong_gram_toi_da_100000_gram'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['khoi_luong_gram_toi_da_100000_gram'], 0, 6) === 'error-') ? substr($order['khoi_luong_gram_toi_da_100000_gram'], 6) : $order['khoi_luong_gram_toi_da_100000_gram'] }}
            </td>
            <td style="{{ (substr($order['dai_cm'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['dai_cm'], 0, 6) === 'error-') ? substr($order['dai_cm'], 6) : $order['dai_cm'] }}
            </td>
            <td style="{{ (substr($order['rong_cm'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['rong_cm'], 0, 6) === 'error-') ? substr($order['rong_cm'], 6) : $order['rong_cm'] }}
            </td>
            <td style="{{ (substr($order['cao_cm'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['cao_cm'], 0, 6) === 'error-') ? substr($order['cao_cm'], 6) : $order['cao_cm'] }}
            </td>
            <td style="{{ (substr($order['gia_tri_hang_hoa'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['gia_tri_hang_hoa'], 0, 6) === 'error-') ? substr($order['gia_tri_hang_hoa'], 6) : $order['gia_tri_hang_hoa'] }}
            </td>
            <td style="{{ (substr($order['shop_tra_ship'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['shop_tra_ship'], 0, 6) === 'error-') ? substr($order['shop_tra_ship'], 6) : $order['shop_tra_ship'] }}
            </td>
            <td style="{{ (substr($order['ma_don_hang_rieng'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['ma_don_hang_rieng'], 0, 6) === 'error-') ? substr($order['ma_don_hang_rieng'], 6) : $order['ma_don_hang_rieng'] }}
            </td>
            <td style="{{ (substr($order['ten_hang_hoa'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['ten_hang_hoa'], 0, 6) === 'error-') ? substr($order['ten_hang_hoa'], 6) : $order['ten_hang_hoa'] }}
            </td>
            <td style="{{ (substr($order['so_luong'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['so_luong'], 0, 6) === 'error-') ? substr($order['so_luong'], 6) : $order['so_luong'] }}
            </td>
            <td style="{{ (substr($order['ghi_chu_them'], 0, 6) === 'error-') ? 'background: red' : '' }}">
                {{ (substr($order['ghi_chu_them'], 0, 6) === 'error-') ? substr($order['ghi_chu_them'], 6) : $order['ghi_chu_them'] }}
            </td>
            <td>{{ $order['id_store'] }}</td>
            <td>{{ $order['id_shop'] }}</td>
        </tr>
    @endforeach
</table>