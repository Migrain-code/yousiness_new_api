<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PackageSaleAddPaymentRequest;
use App\Http\Requests\PackageSaleAddRequest;
use App\Http\Requests\PackageSaleAddUsageRequest;
use App\Http\Resources\BusinessCustomerResource;
use App\Http\Resources\BusinessServiceResource;
use App\Http\Resources\PackageSalePaymentResource;
use App\Http\Resources\PackageSalesResource;
use App\Http\Resources\PackageSaleUsageResource;
use App\Http\Resources\PersonelResource;
use App\Models\PackagePayment;
use App\Models\PackageSale;
use App\Models\PackageUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @group PackageSale
 *
 *
 */
class PackageSaleController extends Controller
{
    /**
     * GET api/business/package-sale
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * </ul>
     * İşletmenin paket satışı listesi sadece bu apiden dönen verileri listede göstermeniz yeterlidir.
     *
     *
     *
     */
    public function index(Request $request)
    {
        $business = $request->user();

        return response()->json([
            'packages' => PackageSalesResource::collection($business->packages),
        ]);
    }

    /**
     * POST api/business/package-sale/payments
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * <li>package_id | numeric | required | Paket id</li>
     * </ul>
     * İd si gönderilen paketin ödemelerinin listesini döndürecek
     *
     *
     *
     */
    public function payments(Request $request)
    {
        $payments = $request->user()->packages()->where('id', $request->package_id)->first()->payeds;
        return response()->json([
            'payments' => PackageSalePaymentResource::collection($payments),
        ]);
    }

    /**
     * POST api/business/package-sale/usages
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * <li>package_id | numeric | required | Paket id</li>
     * </ul>
     * İd si gönderilen paketin kullanım listesini döndürecek
     *
     *
     *
     */
    public function usages(Request $request)
    {
        $usages = $request->user()->packages()->where('id', $request->package_id)->first()->usages;
        return response()->json([
            'usages' => PackageSaleUsageResource::collection($usages),
        ]);
    }

    /**
     * GET api/business/package-sale/create-packet
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * </ul>
     * Bu point ile paket satışı oluşturma sayfasını açtığınızda ihtiyacınız olan tüm bilgiler size döndürülecek
     *
     *
     *
     */
    public function createPacket(Request $request)
    {
        $business = $request->user();
        $businessCustomers = $business->customers()->get();
        $customers = [];
        foreach ($businessCustomers as $customer) {
            $customers[] = $customer->customer;
        }
        $types = [
            [
                'id' => 0,
                'name' => "Seans",
            ],
            [
                'id' => 1,
                'name' => "Dakika",
            ]
        ];
        return response()->json([
            'customers' => $customers,
            'services' => BusinessServiceResource::collection($business->services),
            'types' => $types,
            'personals' => PersonelResource::collection($business->personel)
        ]);
    }

    /**
     * POST api/business/package-sale/add-packet
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * <li>customer_id | string | required | Müşteri Id'si</li>
     * <li>service_id | string | required | Hizmet ID 'si</li>
     * <li>amount | string | required | Adet, Paket Sayısı</li>
     * <li>total | string | required | Toplam Fiyat, Fiyat</li>
     * <li>personel_id | string | required | Personel ID'si</li>
     * <li>package_type | string | required | Paket Türü seans veya dakika</li>
     * <li>seller_date | date | required | Paket Satış Tarihi örnek (14.10.2023)</li>
     * </ul>
     * Bu point ile paket satışı oluşturma işlemini gerçekleştireceksiniz
     *
     *
     *
     */
    public function addPacket(PackageSaleAddRequest $request)
    {
        $translate_date = Carbon::parse($request->seller_date)->format('Y-m-d');

        $packageSale = new PackageSale();
        $packageSale->business_id = $request->user()->id();
        $packageSale->seller_date = $translate_date;
        $packageSale->customer_id = $request->input('customer_id');
        $packageSale->service_id = $request->input('service_id');
        $packageSale->type = $request->input('package_type');
        $packageSale->personel_id = $request->input('personel_id');
        $packageSale->amount = $request->input('amount');
        $packageSale->total = $this->sayiDuzenle($request->total);
        if ($packageSale->save()) {
            return response()->json([
                'status' => "success",
                'message' => "Satış Yapma İşlemi Eklendi"
            ]);
        }
    }
    /**
     * POST api/business/package-sale/add-payment
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * <li>package_id | string | required | Paket Id'si</li>
     * <li>price | string | required | Ödeme Yapılan Tutar</li>
     * <li>amount | string | required | Ödeme Yapılan adet</li>
     * </ul>
     * Bu point ile pakete ödeme eklemi işlemini gerçekleştireceksiniz
     *
     *
     *
     */
    public function addPayment(PackageSaleAddPaymentRequest $request)
    {
        $payment=new PackagePayment();
        $payment->package_id=$request->package_id;
        $payment->price=$request->price;
        $payment->amount=$request->amount;
        if ($payment->save()){
            return response()->json([
                'status'=>"success",
                'message'=> "Pakete Ödeme Eklendi",
                'payment'=>PackageSalePaymentResource::make($payment)
            ]);
        }
    }
    /**
     * POST api/business/package-sale/add-usage
     *
     *
     * <ul>
     * <li>Bearer Token | string | required | Kullanıcı Token</li>
     * <li>package_id | string | required | Paket Id'si</li>
     * <li>personel_id | string | required | Kullanımı yapan personel Id'si</li>
     * <li>amount | string | required | Kullanılan adet</li>
     * <li>operation_date | string | required | İşlemin yapıldığı tarih örn (19.10.2023 19:56:11)</li>
     * </ul>
     * Bu point ile pakete ödeme eklemi işlemini gerçekleştireceksiniz
     *
     *
     *
     */
    public function addUsage(PackageSaleAddUsageRequest $request)
    {
        $findPackage=PackageSale::find($request->package_id);
        if ($findPackage){
            if ($findPackage->amount >= $request->amount)
            {
                $usage=new PackageUsage();
                $usage->package_id=$request->package_id;
                $usage->personel_id=$request->personel_id;
                $usage->amount=$request->amount;
                $usage->created_at=$request->operation_date;
                if ($usage->save()){
                    return response()->json([
                        'status'=>"success",
                        'message' => "Paket Kullanımı Eklendi",
                        'package'=>PackageSaleUsageResource::make($usage)
                    ]);
                }
            }
            else{
                $message="Kullanım Eklerken pakete tanımlı olan kullanım miktarından daha büyük bir değer giremezsiniz. Paketin Maximum Kullanım Miktarı : ".$findPackage->amount;
                return response()->json([
                    'status'=>"warning",
                    'message'=>$message
                ]);
            }
        }
        else{
            return response()->json([
                'status'=>"danger",
                'message'=>'Paket Bilgisi Bulunamadı'
            ]);
        }


    }

    function sayiDuzenle($sayi)
    {
        $sayi = str_replace('.', '', $sayi);
        $sayi = str_replace(',', '.', $sayi);
        $sonuc = floatval($sayi);
        return $sonuc;
    }
}
