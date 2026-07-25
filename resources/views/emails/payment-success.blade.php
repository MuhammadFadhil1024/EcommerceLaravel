<tr>
    <td width="100%" cellpadding="0" cellspacing="0"
        style="box-sizing:border-box;background-color:#ffffff;border-bottom:1px solid #ffffff;border-top:1px solid #ffffff;margin:0;padding:0;width:100%;border:hidden!important">
        <table class="m_8551268127096966539inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
            role="presentation"
            style="box-sizing:border-box;background-color:whitesmoke;border-color:#e8e5ef;border-radius:25px;border-width:1px;margin:0 auto;padding:0;width:570px">
            <tbody style="box-sizing:border-box;">
                <tr>
                    <td class="m_8551268127096966539content-cell"
                        style="box-sizing:border-box;max-width:100vw;padding:32px">
                        
                        <span style="box-sizing:border-box;font-weight:bold">{{ $transaction->user->name }}</span>
                        <br><br>
                        We are pleased to inform you that your payment has been successful. Here are your
                        invoice details:
                        <br><br>
                        
                        <table cellspacing="0" cellpadding="0" style="box-sizing:border-box;border-collapse:collapse;margin-bottom:20px;">
                            <tbody style="box-sizing:border-box;">
                                <tr>
                                    <td width="150px" style="box-sizing:border-box;padding-bottom:5px;">
                                        <strong style="box-sizing:border-box;">Reference No.</strong>
                                    </td>
                                    <td style="box-sizing:border-box;padding-bottom:5px;">{{ $transaction->reference_id }}</td>
                                </tr>
                                <tr>
                                    <td width="150px" style="box-sizing:border-box;padding-bottom:5px;">
                                        <strong style="box-sizing:border-box;">Name</strong>
                                    </td>
                                    <td style="box-sizing:border-box;padding-bottom:5px;">{{ $transaction->user->name }}</td>
                                </tr>
                                <tr>
                                    <td width="150px" style="box-sizing:border-box;padding-bottom:5px;">
                                        <strong style="box-sizing:border-box;">Email</strong>
                                    </td>
                                    <td style="box-sizing:border-box;padding-bottom:5px;">
                                        <a href="mailto:{{ $transaction->user->email }}" target="_blank" style="color:#0000ee;text-decoration:none;">{{ $transaction->user->email }}</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- PERBAIKAN: Jumlah item dibuat dinamis -->
                        <h4 style="box-sizing:border-box;margin-bottom:10px;margin-top:0;">Items ({{ $transaction->items->count() }})</h4>
                        
                        <table cellspacing="0" cellpadding="0" style="box-sizing:border-box;width:100%;border-collapse:collapse;margin-bottom:20px;">
                            <tbody style="box-sizing:border-box;">
                                @foreach($transaction->items as $item)
                                    <tr style="font-weight:500;">
                                        <td style="box-sizing:border-box;border-top:1px solid #dcdcdc;border-bottom:1px solid #dcdcdc;padding:12px 0;">
                                            {{ $item->product->name }} 
                                            <!-- Menambahkan keterangan (x2) jika lebih dari 1 -->
                                            @if($item->quantity > 1)
                                                <span style="font-size: 0.9em; color: #555;">(x{{ $item->quantity }})</span>
                                            @endif
                                        </td>
                                        <td style="box-sizing:border-box;text-align:end;border-top:1px solid #dcdcdc;border-bottom:1px solid #dcdcdc;padding:12px 0;">
                                            <!-- PERBAIKAN: Mengalikan harga dengan kuantitas agar tampil subtotal baris -->
                                            {{ formatRupiah($item->product->price * $item->quantity) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <h4 style="box-sizing:border-box;margin-bottom:10px;margin-top:0;">Payment Summary</h4>
                        
                        <table cellspacing="0" cellpadding="0" style="box-sizing:border-box;width:100%;border-collapse:collapse;">
                            <tbody style="box-sizing:border-box;">
                                <tr>
                                    <td style="box-sizing:border-box;font-weight:bold;width:100%;padding:4px 0;">
                                        Subtotal
                                    </td>
                                    <td style="box-sizing:border-box;text-align:end;font-weight:500;padding:4px 0;">
                                        {{ formatRupiah($transaction->total_payment) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="box-sizing:border-box;font-weight:bold;width:100%;padding:4px 0;">
                                        Courier cost
                                    </td>
                                    <td style="box-sizing:border-box;text-align:end;font-weight:500;padding:4px 0;">
                                        {{ formatRupiah($transaction->courier_cost) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="box-sizing:border-box;font-weight:bold;width:100%;padding:8px 0 0 0;border-top:1px solid #dcdcdc;">
                                        Grand Total
                                    </td>
                                    <td style="box-sizing:border-box;text-align:end;font-weight:500;padding:8px 0 0 0;border-top:1px solid #dcdcdc;">
                                        {{ formatRupiah(($transaction->total_payment + $transaction->courier_cost)) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>