<?php

use Illuminate\Database\Seeder;
use App\Modules\Operators\Models\Entities\PrintTemplates;

class PrintTemplateSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $val = [];
        $val[] = [
            'page_size' => 'A7',
            'type'      => 'doc',
            'html'      => '
                <hr />
                <div><strong>M&atilde;</strong>:&nbsp;{__MA_DH__}
                <p>{__MA_VACH_DH__}</p>
                <hr />
                <div>Sản phẩm:&nbsp;{__DANH_SACH_SP__}</div>
                
                <div>Người nhận:&nbsp;{__TEN_NGUOI_NHAN__}</div>
                
                <div>Tel:&nbsp;{__SDT_NGUOI_NHAN__}</div>
                
                <div>Địa chỉ:&nbsp;{__DIA_CHI_NGUOI_NHAN__}</div>
                
                <div>Ghi ch&uacute;:&nbsp;{__GHI_CHU__}</div>
                
                <div>
                <hr />
                <p>Tổng:&nbsp;<strong>{__TONG_THU__}</strong></p>
                </div>
                </div>
            ',
        ];
        $val[] = [
            'page_size' => 'A7',
            'type'      => 'ngang',
            'html'      => '
                <hr />
                <div><strong>M&atilde;</strong>:&nbsp;{__MA_DH__}
                <p>{__MA_VACH_DH__}</p>
                <hr />
                <div>Sản phẩm:&nbsp;{__DANH_SACH_SP__}</div>
                
                <div>Người nhận:&nbsp;{__TEN_NGUOI_NHAN__}</div>
                
                <div>Tel:&nbsp;{__SDT_NGUOI_NHAN__}</div>
                
                <div>Địa chỉ:&nbsp;{__DIA_CHI_NGUOI_NHAN__}</div>
                
                <div>Ghi ch&uacute;:&nbsp;{__GHI_CHU__}</div>
                
                <div>
                <hr />
                <p>Tổng:&nbsp;<strong>{__TONG_THU__}</strong></p>
                </div>
                </div>
            ',
        ];
        $val[] = [
            'page_size' => 'A6',
            'type'      => 'doc',
            'html'      => '
                <hr />
                <div>
                <p><strong>M&atilde;</strong>:&nbsp;{__MA_DH__}</p>
                
                <p>{__MA_VACH_DH__}</p>
                
                <hr />
                <div>Sản phẩm:&nbsp;{__DANH_SACH_SP__}</div>
                
                <div>Người nhận:&nbsp;{__TEN_NGUOI_NHAN__}</div>
                
                <div>Tel:&nbsp;{__SDT_NGUOI_NHAN__}</div>
                
                <div>Địa chỉ:&nbsp;{__DIA_CHI_NGUOI_NHAN__}</div>
                
                <div>Ghi ch&uacute;:&nbsp;{__GHI_CHU__}</div>
                
                <div>
                <hr />
                <p>Tổng:&nbsp;<strong>{__TONG_THU__}</strong></p>
                </div>
                </div>
            ',
        ];
        $val[] = [
            'page_size' => 'A6',
            'type'      => 'ngang',
            'html'      => '
                <hr />
                <div>
                <p><strong>M&atilde;</strong>:&nbsp;{__MA_DH__}</p>
                
                <p>{__MA_VACH_DH__}</p>
                
                <hr />
                <div>Sản phẩm:&nbsp;{__DANH_SACH_SP__}</div>
                
                <div>Người nhận:&nbsp;{__TEN_NGUOI_NHAN__}</div>
                
                <div>Tel:&nbsp;{__SDT_NGUOI_NHAN__}</div>
                
                <div>Địa chỉ:&nbsp;{__DIA_CHI_NGUOI_NHAN__}</div>
                
                <div>Ghi ch&uacute;:&nbsp;{__GHI_CHU__}</div>
                
                <div>
                <hr />
                <p>Tổng:&nbsp;<strong>{__TONG_THU__}</strong></p>
                </div>
                </div>
            ',
        ];
        $val[] = [
            'page_size' => 'A5',
            'type'      => 'doc',
            'html'      => '
                <div>{__LOGO_DOANH_NGHIEP__}</div>

                <table cellpadding="0" cellspacing="0" style="font-size:13px; margin:0px 0px 20px 0px; width:100%">
                    <tbody>
                        <tr>
                            <td style="border-style:none; text-align:center; vertical-align:middle; width:30%">
                            <div>M&atilde; đơn h&agrave;ng:&nbsp;{__MA_DH__}</div>
                
                            <div>{__MA_VACH_DH__}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div>
                <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%">
                    <tbody>
                        <tr>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN NGƯỜI GỬI</strong>
                
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; line-height:20px; width:100%">
                                <tbody>
                                    <tr>
                                        <td>Người gửi:&nbsp;{__TEN_CUA_HANG__}</td>
                                    </tr>
                                    <tr>
                                        <td>Điện thoại:&nbsp;{__SDT_CUA_HANG__}</td>
                                    </tr>
                                    <tr>
                                        <td>Địa chỉ:&nbsp;{__DIA_CHI_CUA_HANG__}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN CƯỚC PH&Iacute;</strong>
                            <p>Thu COD:&nbsp;<strong>{__TONG_THU__}</strong></p>
                
                            <p>Ghi ch&uacute;:&nbsp;{__GHI_CHU__}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN NGƯỜI NHẬN</strong>
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; line-height:20px; width:100%">
                                <tbody>
                                    <tr>
                                        <td>Người nhận: {__TEN_NGUOI_NHAN__}</td>
                                    </tr>
                                    <tr>
                                        <td>Điện thoại:<strong> </strong>{__SDT_NGUOI_NHAN__}</td>
                                    </tr>
                                    <tr>
                                        <td>Địa chỉ: {__DIA_CHI_NGUOI_NHAN__}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN H&Agrave;NG H&Oacute;A</strong>
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; line-height:20px; width:100%">
                                <tbody>
                                    <tr>
                                        <td style="border-bottom:1px solid #000000; width:65%">T&ecirc;n SP</td>
                                        <td style="border-bottom:1px solid #000000; text-align:center; width:5%">SL</td>
                                    </tr>
                                    <tr>
                                        <td style="width:65%">{__DANH_SACH_SP__}</td>
                                        <td style="text-align:center; width:5%">{__SL_SP__}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            ',
        ];
        $val[] = [
            'page_size' => 'A5',
            'type'      => 'ngang',
            'html'      => '
                <div>{__LOGO_DOANH_NGHIEP__}</div>

                <table cellpadding="0" cellspacing="0" style="font-size:13px; margin:0px 0px 20px 0px; width:100%">
                    <tbody>
                        <tr>
                            <td style="border-style:none; text-align:center; vertical-align:middle; width:30%">
                            <div>M&atilde; đơn h&agrave;ng:&nbsp;{__MA_DH__}</div>
                
                            <div>{__MA_VACH_DH__}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div>
                <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%">
                    <tbody>
                        <tr>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN NGƯỜI GỬI</strong>
                
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; line-height:20px; width:100%">
                                <tbody>
                                    <tr>
                                        <td>Người gửi:&nbsp;{__TEN_CUA_HANG__}</td>
                                    </tr>
                                    <tr>
                                        <td>Điện thoại:&nbsp;{__SDT_CUA_HANG__}</td>
                                    </tr>
                                    <tr>
                                        <td>Địa chỉ:&nbsp;{__DIA_CHI_CUA_HANG__}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN CƯỚC PH&Iacute;</strong>
                            <p>Thu COD:&nbsp;<strong>{__TONG_THU__}</strong></p>
                
                            <p>Ghi ch&uacute;:&nbsp;{__GHI_CHU__}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN NGƯỜI NHẬN</strong>
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; line-height:20px; width:100%">
                                <tbody>
                                    <tr>
                                        <td>Người nhận: {__TEN_NGUOI_NHAN__}</td>
                                    </tr>
                                    <tr>
                                        <td>Điện thoại:<strong> </strong>{__SDT_NGUOI_NHAN__}</td>
                                    </tr>
                                    <tr>
                                        <td>Địa chỉ: {__DIA_CHI_NGUOI_NHAN__}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                            <td style="vertical-align:top; width:35%"><strong>TH&Ocirc;NG TIN H&Agrave;NG H&Oacute;A</strong>
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; line-height:20px; width:100%">
                                <tbody>
                                    <tr>
                                        <td style="border-bottom:1px solid #000000; width:65%">T&ecirc;n SP</td>
                                        <td style="border-bottom:1px solid #000000; text-align:center; width:5%">SL</td>
                                    </tr>
                                    <tr>
                                        <td style="width:65%">{__DANH_SACH_SP__}</td>
                                        <td style="text-align:center; width:5%">{__SL_SP__}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            ',
        ];

        PrintTemplates::insert($val);
    }
}
