**LAPORAN TUGAS AKHIR**

**"IMPLEMENTASI PROGESIVE WEB APP(PWA) DENGAN PROTOKOL VAPID PADA SISTEM RESERVASI BARBERSHOP SEBAGAI NOTIFIKASI PENGINGAT PELANGGAN"**

Diajukan untuk memenuhi salah satu syarat untuk memperoleh gelar Sarjana Teknik Informatika

![UDINUS - Universitas Dian Nuswantoro](media/image1.png){width="1.968503937007874in" height="1.968503937007874in"}

Disusun oleh:

  --------------------------------------------------------------------------
  **NAMA**                  **:**   **YOVA FEBRIAN PRADITA**
  ------------------------- ------- ----------------------------------------
  **NIM**                   **:**   **A11.2022.14095**

  **PROGAM STUDI**          **:**   **TEKNIK INFORMATIKA**
  --------------------------------------------------------------------------

**FAKULTAS ILMU KOMPUTER**

**UNIVERSITAS DIAN NUSWANTORO**

**SEMARANG**

**2026**

# DAFTAR ISI {#daftar-isi .unnumbered}

#  {#section .TOC-Heading .unnumbered}

[DAFTAR ISI [2](#daftar-isi)](#daftar-isi)

[DAFTAR TABEL [4](#daftar-tabel)](#daftar-tabel)

[BAB I PENDAHULUAN [5](#pendahuluan)](#pendahuluan)

[1.1 Latar Belakang [5](#latar-belakang)](#latar-belakang)

[1.2 Rumusan Masalah [8](#rumusan-masalah)](#rumusan-masalah)

[1.3 Batasan Masalah (Ruang lingkup) [8](#batasan-masalah-ruang-lingkup)](#batasan-masalah-ruang-lingkup)

[1.4 Tujuan Penelitian [9](#tujuan-penelitian)](#tujuan-penelitian)

[1.5 Manfaat Penelitian [10](#manfaat-penelitian)](#manfaat-penelitian)

[1.5.1 Manfaat Bagi Penusil [10](#manfaat-bagi-penusil)](#manfaat-bagi-penusil)

[1.5.1 Bagi Akademis [10](#bagi-akademis)](#bagi-akademis)

[1.5.2 Bagi Industri [11](#bagi-industri)](#bagi-industri)

[BAB II LANDASAN TEORI [12](#landasan-teori)](#landasan-teori)

[2.1 Tinjauan Studi [12](#tinjauan-studi)](#tinjauan-studi)

[2.1.1 Mekanisme Web Push Notification [16](#mekanisme-web-push-notification)](#mekanisme-web-push-notification)

[2.1.2 Protokol VAPID untuk Otentikasi Pengiriman Notifikasi [17](#protokol-vapid-untuk-otentikasi-pengiriman-notifikasi)](#protokol-vapid-untuk-otentikasi-pengiriman-notifikasi)

[2.1.3 Logika Otomatisasi Status Reservasi dan Mitigasi No-Show [18](#logika-otomatisasi-status-reservasi-dan-mitigasi-no-show)](#logika-otomatisasi-status-reservasi-dan-mitigasi-no-show)

[2.2 Tinjauan Pustaka [18](#tinjauan-pustaka)](#tinjauan-pustaka)

[2.2.1 REST API [18](#rest-api)](#rest-api)

[2.2.2 Laravel Task Scheduling [19](#laravel-task-scheduling)](#laravel-task-scheduling)

[2.2.3 Proressive Web App (PWA) [19](#proressive-web-app-pwa)](#proressive-web-app-pwa)

[2.2.4 Framework Laravel [19](#framework-laravel)](#framework-laravel)

[2.2.5 PHP [20](#php)](#php)

[2.2.6 Visual Studio Code [20](#visual-studio-code)](#visual-studio-code)

[2.2.7 MySQL [20](#mysql)](#mysql)

[2.2.8 Voluntary Application Server Identification (VAPID) [20](#voluntary-application-server-identification-vapid)](#voluntary-application-server-identification-vapid)

[2.2.9 Service Worker [21](#service-worker)](#service-worker)

[2.2.10 Web Push Notification [21](#web-push-notification)](#web-push-notification)

[2.2.11 Diagram UML [21](#diagram-uml)](#diagram-uml)

[2.3 Kerangka Pemikiran [27](#kerangka-pemikiran)](#kerangka-pemikiran)

[BAB III METODE PENELITIAN [29](#metode-penelitian)](#metode-penelitian)

[3.1 Metode Pengumpulan Data [29](#metode-pengumpulan-data)](#metode-pengumpulan-data)

[3.2 Analisis Data [31](#analisis-data)](#analisis-data)s

[3.2.1 Analisis Kebutuhan Fungsional [31](#analisis-kebutuhan-fungsional)](#analisis-kebutuhan-fungsional)

[3.3 Metode yang Diusulkan [33](#metode-yang-diusulkan)](#metode-yang-diusulkan)

[3.3.1 Perancangan Kebutuhan [34](#perancangan-kebutuhan)](#perancangan-kebutuhan)

[3.3.2 Desain Pengguna [34](#desain-pengguna)](#desain-pengguna)

[3.3.3 Tahapan Konstruksi [35](#tahapan-konstruksi)](#tahapan-konstruksi)

[3.3.4 Pengalihan [35](#pengalihan)](#pengalihan)

[3.4 Metode Pengujian [36](#metode-pengujian)](#metode-pengujian)

[3.4.1 Black Box Testing [36](#black-box-testing)](#black-box-testing)

[3.4.2 User Acceptance Testing (UAT) [38](#user-acceptance-testing-uat)](#user-acceptance-testing-uat)

[DAFTAR PUSTAKA [40](#daftar-pustaka)](#daftar-pustaka)

# DAFTAR TABEL {#daftar-tabel .unnumbered}

[Table 2. 1 UseCase Diagram [24](#_Toc219642063)](#_Toc219642063)

[Table 2. 2 Activity Diagram [25](#_Toc218532545)](#_Toc218532545)

[Table 2. 3 Class Diagram [26](#_Toc218532546)](#_Toc218532546)

[Table 2. 4 Kerangka Pemikiran [28](#_Toc218532547)](#_Toc218532547)

#  PENDAHULUAN

## Latar Belakang 

Perkembangan gaya hidup masyarakat modern telah mendorong transformasi signifikan dalam sektor layanan jasa, termasuk industri *barbershop*. *Barbershop* kini tidak lagi sekadar menyediakan layanan potong rambut, tetapi telah berevolusi menjadi layanan profesional yang menuntut proses operasional yang cepat, terstruktur, dan efisien (Andi Juandi et al., 2025). Seiring dengan meningkatnya volume pelanggan dan kompleksitas layanan yang ditawarkan, tuntutan terhadap sistem pelayanan yang modern, dapat diandalkan, dan responsif menjadi semakin kuat. Modernisasi ini merupakan kunci bagi Usaha Mikro, Kecil, dan Menengah (UMKM) jasa untuk tetap relevan dan kompetitif di tengah era digital.

Meskipun terjadi pertumbuhan yang pesat dalam industri ini, banyak *barbershop*, termasuk Nusantara Pangkas Rambut, masih mengandalkan cara kerja manual dalam pengelolaan layanan dan pencatatan jadwal harian. Proses manual ini menyebabkan antrean tidak teratur, kesalahan pencatatan, dan kasus *double booking* yang berdampak pada kualitas layanan dan kepuasan pelanggan(Trianasari & Debataraja, 2020). Permasalahan tersebut menunjukkan adanya kebutuhan mendesak untuk melakukan modernisasi sistem reservasi dan manajemen operasional agar lebih efektif dan profesional. Lebih lanjut, permasalahan operasional yang dihadapi juga mencakup proses reservasi yang masih dilakukan melalui kedatangan langsung atau komunikasi informal seperti telepon dan pesan singkat, mengakibatkan data tidak terpusat, mudah hilang, serta sulit ditelusuri untuk kepentingan analisis dan audit (Haidar et al., 2025). Studi-studi sebelumnya pada berbagai *barbershop* juga menunjukkan bahwa absennya digitalisasi menyebabkan antrean tidak terorganisir, histori pelanggan tidak terdokumentasi dengan baik, dan laporan operasional menjadi tidak akurat (Almaarij et al., 2025) . Kondisi serupa ditemukan pada Nusantara Pangkas Rambut, di mana pencatatan data pelanggan, jadwal *barber*, dan transaksi harian masih dilakukan secara manual, sehingga menyulitkan pemilik untuk memantau performa bisnis secara menyeluruh.

Sebagai upaya untuk mengatasi berbagai kendala tersebut, digitalisasi menjadi langkah strategis agar Nusantara Pangkas Rambut dan sejenisnya dapat tetap kompetitif di era modern(Almaarij et al., 2025). Pengembangan sistem informasi reservasi berbasis *web* maupun aplikasi *mobile* terbukti efektif dalam mengatasi kelemahan sistem manual, di mana penerapan sistem reservasi *online* dapat mengurangi waktu tunggu pelanggan, meminimalkan antrean, dan meningkatkan kenyamanan dalam memperoleh layanan(Febrian & Ichwani, n.d.). Namun, sistem reservasi *online* juga menghadirkan tantangan baru, terutama dalam mengelola pelanggan yang datang terlambat atau tidak hadir (*no-show*).

Ketika pelanggan telah memesan slot waktu tetapi tidak hadir, slot tersebut menjadi terbuang percuma karena tidak dapat langsung dialihkan ke pelanggan lain. Hal ini berdampak pada optimalisasi waktu kerja *barber* serta menurunkan efisiensi operasional Nusantara Pangkas Rambut secara keseluruhan (Haidar et al., 2025). Oleh karena itu, dibutuhkan sebuah solusi yang tidak hanya mengelola pemesanan, tetapi juga dapat menjalankan mekanisme proaktif untuk meminimalkan kerugian akibat *idle time*

Untuk menjawab permasalahan utama *no-show*, penelitian ini mengusulkan pengembangan sistem berbasis Progressive Web App (PWA) (Hafid Hanifan, 2024) yang terintegrasi dengan fitur Web Push Notification otomatis.  PWA dipilih karena kemampuannya menghadirkan pengalaman layaknya aplikasi native, termasuk dukungan service worker yang memungkinkan pengiriman notifikasi latar belakang meskipun aplikasi web atau browser tidak sedang dibuka(Aripin & Somantri, 2021; Hanifan & Fajri, 2024). Hal ini memastikan notifikasi *reminder* terkirim dan terlihat oleh pelanggan meskipun *browser* atau aplikasi tidak sedang dibuka, sehingga memaksimalkan efektivitas pengingat.

Sistem dibangun menggunakan framework Laravel karena arsitektur Model-View-Controller (MVC) yang terstruktur, tingkat keamanan tinggi, serta fleksibilitas dalam pengembangan fitur kompleks. Mekanisme Web Push Notification diimplementasikan menggunakan Laravel Web Push yang memanfaatkan protokol VAPID (Voluntary Application Server Identification) sebagai metode otentikasi standar, memungkinkan server mengirim notifikasi secara mandiri tanpa ketergantungan pada layanan pihak ketiga seperti Firebase(Beverloo, 2017; Imron et al., 2020)Integrasi teknologi ini diharapkan menghadirkan solusi notifikasi yang aman, skalabel, dan hemat biaya bagi UMKM.

Mekanisme notifikasi ini dirancang untuk mengirimkan notifikasi pengingat sebelum batas waktu reservasi habis. Jika pelanggan tidak datang dalam batas waktu toleransi yang ditetapkan (misalnya 30 menit), sistem akan secara otomatis mengubah status reservasi menjadi "hangus" atau "kedaluwarsa" dan mengirimkan notifikasi kepada pelanggan. Penelitian sebelumnya juga menunjukkan bahwa notifikasi yang relevan dan tepat waktu mampu meningkatkan pengalaman pengguna dan mendukung efektivitas layanan secara keseluruhan (Devyanti et al., 2025; Gavilan & Martinez-Navarro, 2022).

Dengan memfokuskan pengembangan pada implementasi Laravel Web Push Notification dengan VAPID, penelitian ini menghadirkan solusi yang efisien dan skalabel untuk mengatasi masalah *no-show*. Sistem yang dikembangkan diharapkan mampu meningkatkan kualitas layanan Nusantara Pangkas Rambut, mengoptimalkan alur kerja *barber*, serta memberikan kontribusi praktis dalam pengembangan sistem reservasi *online* yang lebih modern dan responsif di sektor UMKM jasa.

## Rumusan Masalah

Berdasarkan latar belakang masalah yang telah diuraikan, maka rumusan masalah dalam penelitian ini adalah sebagai berikut:

1.  Bagaimana merancang dan mengimplementasikan arsitektur Progressive Web App (PWA) pada sistem reservasi Nusantara Pangkas Rambut, yang mendukung notifikasi latar belakang (*background notification*)?

2.  Bagaimana mengintegrasikan *framework* Laravel dengan protokol VAPID untuk merancang dan mengimplementasikan fitur Web Push Notification otomatis sebagai pengingat keterlambatan pelanggan secara mandiri dan *real-time*?

3.  Bagaimana sistem dapat secara otomatis menjalankan logika bisnis pada *backend* Laravel untuk mengubah status reservasi menjadi \"hangus\" (kedaluwarsa), berdasarkan notifikasi yang telah terkirim dan batas waktu toleransi yang ditetapkan, guna mengoptimalkan kembali slot layanan di Nusantara Pangkas Rambut?

## Batasan Masalah (Ruang lingkup)

Untuk memastikan penelitian ini tetap fokus dan mendalam, maka ditetapkan batasan masalah sebagai berikut:

1.  Penelitian ini berfokus pada pengembangan sistem reservasi layanan *barbershop* berbasis *web* yang dilengkapi dengan fitur *push notification* otomatis sebagai pengingat keterlambatan pelanggan.

2.  Arsitektur yang dikembangkan adalah Progressive Web App (PWA), di mana pengguna menerima notifikasi melalui *browser* setelah memberikan izin notifikasi (*Web Push*), bukan melalui aplikasi *native* Android atau iOS.

3.  Implementasi notifikasi menggunakan protokol VAPID (*Voluntary Application Server Identification*) yang terintegrasi pada *framework* Laravel di sisi *backend* untuk pengiriman notifikasi yang mandiri dan *real-time*.

4.  Fitur *push notification* hanya difungsikan sebagai pengingat keterlambatan bagi pelanggan yang telah melakukan reservasi.

5.  Mekanisme otomatisasi status reservasi menjadi \"hangus\" (kedaluwarsa) dijalankan berdasarkan batas waktu toleransi keterlambatan yang ditetapkan setelah notifikasi terkirim.

6.  Penelitian ini hanya mencakup 4 jenis pengguna utama, yaitu Super Admin, Admin Cabang, Kasir, dan User (Pelanggan).

7.  Aspek lain di luar fokus inti, seperti sistem pembayaran *online* terintegrasi, pengelolaan gaji *barber*, atau integrasi pihak ketiga selain *Web Push Service*, tidak dibahas secara mendalam.

## Tujuan Penelitian

Tujuan utama dari penelitian ini adalah untuk menjawab rumusan masalah yang telah ditetapkan. Adapun tujuan penelitian ini adalah sebagai berikut:

1.  Untuk merancang dan mengimplementasikan arsitektur Progressive Web App (PWA) pada sistem reservasi Nusantara Pangkas Rambut guna mendukung firut notifikasi latar belakang (*background notification*).

2.  Untuk menganalisis dan mengintegrasikan *framework* Laravel dengan protokol VAPID dalam merancang dan mengimplementasikan fitur Web Push Notification otomatis sebagai mekanisme pengingat keterlambatan pelanggan secara mandiri dan *real-time*.

3.  Untuk menganalisis dan mengimplementasikan fungsi otomatisasi pengubahan status reservasi menjadi \"hangus\" (kedaluwarsa) berdasarkan *push notification* dan batas waktu toleransi guna mengoptimalkan kembali slot layanan di Nusantara Pangkas Rambut.

## Manfaat Penelitian

Penelitian ini diharapkan dapat memberikan manfaat yang signifikan, baik bagi penulis, bagi pengembangan ilmu pengetahuan (akademis), maupun bagi pelaku usaha (industri), khususnya di sektor jasa *barbershop*:

### Manfaat Bagi Penusil

1.  Memperoleh dan meningkatkan kemampuan praktis dalam merancang dan mengembangkan sistem berbasis *web* menggunakan arsitektur Progressive Web App (PWA) dan *framework* Laravel dengan pendekatan multi-peran pengguna.

2.  Memperoleh pemahaman dan pengalaman praktis mengenai implementasi sistem Web Push Notification berbasis protokol VAPID sebagai solusi komunikasi *real-time* yang mandiri dan aman antara sistem dan pengguna.

3.  Menambah pengalaman dalam menganalisis dan mengelola proses bisnis digital, khususnya dalam konteks manajemen reservasi dan implementasi logika pengingat pelanggan otomatis yang berdampak langsung pada efisiensi operasional.

### Bagi Akademis

1.  Menjadi referensi ilmiah bagi penelitian selanjutnya yang berfokus pada implementasi *push notification* berbasis *web* untuk meningkatkan efektivitas sistem reservasi *online*, terutama dalam konteks mitigasi kerugian akibat kasus *no-show*.

2.  Menjadi kontribusi ilmiah dalam bidang rekayasa perangkat lunak, khususnya terkait penerapan arsitektur PWA yang dikombinasikan dengan notifikasi otomatis dan mekanisme pengubahan status reservasi otomatis dalam sistem layanan berbasis waktu.

3.  Menyediakan studi kasus bagi mahasiswa atau peneliti lain yang ingin mengembangkan sistem multi-peran (Super Admin, Admin Cabang, Kasir, User) yang terintegrasi di sektor jasa digital.

### Bagi Industri

1.  Membantu meningkatkan efisiensi operasional *barbershop* dengan mengurangi beban kerja manual (seperti menghubungi pelanggan yang terlambat) dan meminimalkan potensi kerugian akibat slot layanan yang terbuang (*idle time*).

2.   Menyediakan sistem yang dapat meningkatkan kepuasan pelanggan melalui proses reservasi yang lebih teratur dan adanya notifikasi *real-time* yang membantu pelanggan datang tepat waktu.

3.  Menyediakan model sistem reservasi yang dilengkapi notifikasi otomatis dan mekanisme status hangus, sehingga dapat dijadikan acuan bagi UMKM di sektor jasa lain seperti salon, klinik kecantikan, atau bengkel yang memiliki tantangan manajemen slot waktu.

#   LANDASAN TEORI

## Tinjauan Studi

Tinjauan Studi Pada penelitian ini penulis menggunakan sumber referensi yang diperoleh dari jurnal dan skripsi yang digunakan sebagai acuan dalam menyusun penelitian. Adapun penelitian terdahulu yang berkaitan dan digunakan sebagai referensi adalah sebagai berikut.

Pada penelitian yang dilakukan (Febrian & Ichwani, n.d.), mereka melakukan rancang bangun aplikasi booking online layanan potongan rambut berbasis website menggunakan REST API pada Kumaito Barbershop. Penelitian ini menggunakan metode Extreme Programming (XP) untuk mengatasi ketidakefisienan sistem manual. Hasil penelitian menunjukkan bahwa aplikasi tersebut berhasil mempermudah proses pemesanan dan pembayaran, namun belum menyentuh aspek notifikasi otomatis untuk mitigasi no-show.

Penelitian selanjutnya dilakukan oleh (Almaarij et al., 2025) tentang perancangan sistem informasi reservasi dan manajemen pelanggan di Polka Barbershop menggunakan framework Laravel. Penelitian ini berfokus pada digitalisasi transaksi dan pengelolaan data pelanggan untuk mengurangi antrean yang tidak teratur. Hasil dari penelitian tersebut adalah sistem yang mampu meningkatkan kecepatan operasional, meskipun belum mengintegrasikan fitur pengingat proaktif berbasis web push.

Pada penelitian yang dilakukan oleh(Fadli et al., 2024) mereka mengimplementasikan push notification pada sistem booking perlengkapan outdoor. Teknologi yang digunakan adalah Firebase Cloud Messaging (FCM). Hasil penelitian menunjukkan bahwa penerapan notifikasi secara real-time dapat meningkatkan efisiensi komunikasi antara pengelola dan pengguna, namun implementasinya masih bergantung pada layanan pihak ketiga milik Google.

Penelitian berikutnya dilakukan (Hanifan & Fajri, 2024) mengenai analisis dan implementasi Progressive Web App (PWA) serta fitur notifikasi pada sistem VolHub. Penelitian ini menyoroti penggunaan PWA untuk meningkatkan reliabilitas sistem melalui fitur akses offline dan push notification. Hasil pengujian menunjukkan peningkatan performa sistem secara signifikan, yang membuktikan efektivitas PWA dalam memberikan informasi secara tepat waktu kepada pengguna melalui service worker.

Terakhir, penelitian yang dilakukan oleh(Imron et al., 2020) membahas tentang implementasi sistem peminjaman sarana dan prasarana dengan fitur notifikasi. Penelitian ini bertujuan membantu proses pengelolaan aset secara akurat dan hasil pengujian menunjukkan bahwa notifikasi berhasil meminimalisir keterlambatan pengembalian alat. Namun, penelitian ini masih mengandalkan notifikasi berbasis SMS atau mekanisme konvensional, dan belum menerapkan standar otentikasi server aplikasi mandiri (VAPID) untuk pengiriman Web Push Notification yang lebih efisien.

Tabel 1 State-of-the-art

  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  **No**   **Judul**                                                                                                                                              **Masalah**                                                                                                                 **Metode**                                                                                      **Hasil**
  -------- ------------------------------------------------------------------------------------------------------------------------------------------------------ --------------------------------------------------------------------------------------------------------------------------- ----------------------------------------------------------------------------------------------- -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  1        Rancang Bangun Aplikasi Booking Online Layanan Potongan Rambut Berbasis Website Menggunakan REST API (Febrian & Ichwani, n.d.)                         Sistem pemesanan dan pembayaran manual (Kumaito Barbershop) menyebabkan antrean tidak efisien dan proses transaksi manual   Extreme Programming (XP), REST API, Website (HTML/PHP), Integrasi Pembayaran *Online*.          Berhasil mengurangi antrean dan waktu tunggu. Kesenjangan: Tidak memiliki fitur *reminder* untuk mengatasi *no-show* dan *idle time* slot.

  2        Design of Reservation and Customer Management Information System at Polka Barbershop Using Laravel Framework(Almaarij et al., 2025)                    Reservasi dan data pelanggan masih manual, menyebabkan antrean tidak terorganisir dan kesulitan laporan                     Menggunakan metode Waterfall Model, Laravel Framework, Integrasi *Payment Gateway* (Midtrans)   Sistem meningkatkan efisiensi operasional dan kecepatan layanan. Kesenjangan: Sistem hanya berfokus pada manajemen data dan tidak mengimplementasikan *Web Push* atau logika otomatis status \"hangus\" untuk mitigasi *no-show*.

  3        Penerapan Push Notification Booking Perlengkapan Outdoor Menggunakan Firebase Cloud Messaging (Fadli et al., 2024)                                     Minimnya informasi *real-time* ketersediaan barang pada penyewaan perlengkapan *outdoor*.                                   Firebase Cloud Messaging (FCM), Laravel, Push Notification                                      Notifikasi *real-time* berhasil dikirim, meningkatkan efisiensi komunikasi. Kesenjangan: Menggunakan Firebase Cloud Messaging (FCM), yang bergantung pada layanan pihak ketiga. Penelitian ini menonjolkan solusi mandiri menggunakan VAPID.

  4        ANALISIS DAN IMPLEMENTASI PROGRESSIVE WEB APP (PWA) SERTA FITUR NOTIFIKASI PADA SISTEM INFORMASI PENDAFTARAN VOLUNTEER VOLHUB(Hanifan & Fajri, 2024)   Keandalan sistem pendaftaran *volunteer* rendah dan membutuhkan *background notification* untuk informasi *real-time*.      Progressive Web App (PWA), Service Worker, Push Notifications                                   PWA meningkatkan performa dan keandalan sistem, mendukung notifikasi latar belakang. Kesenjangan: Tidak fokus pada protokol VAPID sebagai solusi otentikasi mandiri, dan tidak memiliki mekanisme otomatisasi status \"hangus\" berbasis waktu.

  5        Implementasi Push Notification Pada Sistem Peminjaman Sarana dan Prasarana Berbasis Website(Imron et al., 2020)                                        Pencatatan peminjaman manual, kesulitan melacak keterlambatan pengembalian sarana dan prasarana.                            Metode Waterfall, Web Push Notification                                                         Sistem meminimalisir keterlambatan pengembalian. Kesenjangan: Belum menggunakan protokol VAPID untuk otentikasi mandiri dan belum memiliki logika otomatisasi status \"hangus\" untuk membebaskan aset secara sistematis.
  ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

Pembaruan (novelty) penelitian ini terletak pada penerapan konsep Progressive Web App (PWA) sebagai arsitektur utama dalam sistem reservasi barbershop yang terintegrasi dengan mekanisme Web Push Notification berbasis protokol VAPID.

Berbeda dengan penelitian sebelumnya yang umumnya hanya mengimplementasikan push notification sebagai fitur tambahan atau masih bergantung pada layanan pihak ketiga seperti Firebase Cloud Messaging (FCM), penelitian ini menempatkan PWA sebagai solusi inti untuk menjamin pengiriman notifikasi secara andal meskipun aplikasi web telah ditutup oleh pengguna. Melalui pemanfaatan service worker pada arsitektur PWA, sistem mampu menjalankan background notification secara konsisten dan real-time.

Protokol Voluntary Application Server Identification (VAPID) digunakan sebagai mekanisme autentikasi mandiri dalam pengiriman notifikasi, sehingga sistem tidak memerlukan ketergantungan pada platform eksternal. Integrasi PWA dan VAPID ini memungkinkan sistem reservasi memberikan pengingat otomatis yang aman, efisien, dan bersifat independen, sekaligus mendukung logika bisnis otomatisasi status reservasi untuk mitigasi no-show.

Dengan demikian, penelitian ini menawarkan pendekatan yang lebih komprehensif dibandingkan penelitian terdahulu, yaitu kombinasi PWA sebagai penjamin background notification dan VAPID sebagai protokol autentikasi mandiri, yang secara langsung berkontribusi pada peningkatan efisiensi operasional dan keandalan sistem reservasi barbershop.

Implementasi ini juga memberikan nilai ekonomis pada UMKM seperti Nusantara Pangkas Rambut karena menghilangkan biaya langganan layanan pihak ketiga seperti Firebase Cloud Messaging.

### Mekanisme Web Push Notification

> Dalam penelitian ini, Web Push Notification diimplementasikan sebagai bagian dari arsitektur Progressive Web App (PWA), yang memungkinkan sistem tetap aktif melalui service worker meskipun aplikasi web telah ditutup oleh pengguna.
>
> Pengembangan sistem reservasi digital menghadirkan tantangan utama berupa penanganan pelanggan yang terlambat atau tidak hadir (*no-show*) (Haidar et al., 2025). Untuk mengatasi masalah ini, digunakan teknologi Web Push Notification yang merupakan sistem komunikasi proaktif yang memungkinkan aplikasi *server* menyampaikan pesan singkat kepada pengguna. Notifikasi ini dikenal mampu meningkatkan pengalaman pengguna karena merupakan bentuk komunikasi proaktif yang relevan dan tepat waktu(Gavilan & Martinez-Navarro, 2022).
>
> Dalam arsitektur *web*, notifikasi ini dioperasikan melalui Service Worker. *Service Worker* adalah skrip JavaScript yang dijalankan oleh *browser* di latar belakang dan terpisah dari skrip halaman utama. Peran *Service Worker* sangat vital karena memungkinkan pengiriman notifikasi latar belakang (*background notification*), yang memastikan notifikasi pengingat terkirim dan terlihat oleh pelanggan meskipun aplikasi *web* atau *browser* tidak sedang dibuka.

### Protokol VAPID untuk Otentikasi Pengiriman Notifikasi

> Untuk menjamin pengiriman *Web Push Notification* yang aman dan terotentikasi, sistem ini mengimplementasikan protokol VAPID (Voluntary Application Server Identification). VAPID berfungsi sebagai metode otentikasi standar yang memungkinkan *server* mengirim notifikasi secara mandiri tanpa ketergantungan pada layanan pihak ketiga seperti(Beverloo, 2017).
>
> Penggunaan VAPID menjadi solusi yang skalabel dan hemat biaya bagi UMKM seperti Nusantara Pangkas Rambut, karena menghilangkan kebutuhan integrasi dan ketergantungan pada *vendor* layanan *cloud* perantara. Mekanisme ini memastikan bahwa *backend* Laravel dapat mengirimkan pesan secara *real-time* dan mandiri ke *endpoint* pelanggan menggunakan arsitektur REST API (Bima Pratama & Triawan, 2024).

### Logika Otomatisasi Status Reservasi dan Mitigasi No-Show

> Fitur *Web Push Notification* tidak hanya berfungsi sebagai pengingat (*reminder*), tetapi juga bertindak sebagai *trigger* untuk menjalankan logika bisnis otomatis. Logika otomatisasi ini dirancang khusus untuk mengatasi kerugian operasional akibat *idle time* slot yang terbuang karena *no-show* (Haidar et al., 2025).
>
> Mekanisme ini bekerja dengan memicu notifikasi pengingat sebelum batas waktu reservasi habis. Jika pelanggan yang telah menerima notifikasi tersebut tidak datang dalam batas waktu toleransi yang ditetapkan (misalnya, kurang lebih 30 menit), *backend* sistem (menggunakan *scheduler* Laravel) akan secara otomatis mengubah status reservasi menjadi \"hangus\" atau \"kedaluwarsa\". Tindakan otomatis ini memastikan slot waktu dapat segera tersedia kembali dan dialokasikan kepada pelanggan lain (*walk-in*), sehingga meningkatkan efisiensi operasional Nusantara Pangkas Rambut secara keseluruhan

## Tinjauan Pustaka

Tinjauan pustaka ini berisi uraian mendalam mengenai konsep-konsep teknis dan teori yang menjadi landasan utama penelitian. Bagian ini membahas arsitektur perangkat lunak, mekanisme keamanan, kerangka kerja pengembangan, serta teknologi penyimpanan data yang digunakan dalam perancangan sistem.

### REST API

> RESTful API merupakan tipe arsitektur dari Application Programming Interface atau API. RESTful API terkadang disebut juga sebagai RESTful web service atau REST API. REST atau Representational State Transfer adalah gaya arsitektur dan pendekatan komunikasi yang umum digunakan dalam proses pengembangan web service. RESTful API memungkinkan sistem yang berbeda untuk saling berkomunikasi(Kaniya et al., 2022).

### Laravel Task Scheduling

> Task Scheduling pada Laravel merupakan fitur yang memungkinkan pengembang untuk menjadwalkan perintah (command) secara otomatis dan berkala di sisi server. Fitur ini menggantikan mekanisme Cron Job tradisional yang kompleks dengan sintaks yang lebih ekspresif dan terintegrasi dalam aplikasi. Dalam penelitian ini, Laravel Scheduler berperan sebagai komponen utama untuk menjalankan logika otomatisasi, seperti memicu pengiriman pesan pengingat melalui Web Push beberapa menit sebelum waktu reservasi dimulai, serta melakukan validasi berkala untuk mengubah status reservasi menjadi \"hangus\" secara otomatis apabila pelanggan tidak melakukan check-in dalam batas waktu toleransi yang telah ditentukan (Rahmawati & Mulyono, 2018)

### Proressive Web App (PWA)

> Progressive Web App (PWA) adalah sebuah teknologi baru yang dirancang dan dikembangkan oleh Google pada Juni 2015 untuk mengatasi keterbatasan browser seluler dan aplikasi native. Progressive web app menggunakan kemampuan web modern yang menggambarkan koleksi teknologi, konsep desain, dan API web yang bekerja bersama-sama untuk menghadirkan pengalaman pengguna seperti aplikasi native (Herman, 2023).

### Framework Laravel 

> Laravel adalah sebuah kerangka kerja PHP opensource yang dirancang untuk menyederhanakan proses pengembangan aplikasi web. Dengan mengadopsi arsitektur Model View Controller (MVC) dan menyediakan berbagai fitur serta pustaka yang kaya. Laravel menawarkan pendekatan yang lebih modern dan ekspresif dalam pengembangan aplikasi web. Dengan Laravel, pengembang dapat membangun aplikasi yang skalabel, aman, dan mudah dipelihara (Andi Juandi et al., 2025).

### PHP

> *Hypertext Preprocessor* atau PHP adalah Bahasa *server-side* *scripting* yang terdapat pada *HTML* untuk membuat *web* dinamis. PHP menggunakan server-*side* *scripting* yang mana berarti sintaks dan perintah-perintah di *PHP* akan dieksekusi deserver lalu dikirim ke *Browser* dalam bentuk HTML.(Handika Siregar et al., 2018).

### Visual Studio Code

> *Visual Studio Code* (VS Code) ini adalah sebuah teks editor ringan dan handal yang dibuat oleh *Microsoft* untuk sistem operasi multiplatform, artinya tersedia juga untuk versi *Linux*, *Mac*, dan *Windows*. Teks editor ini secara langsung mendukung bahasa pemrograman *JavaScript, Typescript, dan Node.js,* serta bahasa pemrograman lainnya dengan bantuan plugin yang dapat dipasang *via* *marketplace* Visual Studio Code (seperti *C++, C#, Python, Go, Java,* dst).(Sains et al., 2022)

### MySQL

> MySQL merupakan sistem database yang banyak digunakan untuk pengembangan aplikasi web. Alasannya mungkin karena gratis, pengelolaan datanya sederhana, memiliki tingkat keamanan yang bagus, mudah diperoleh, dan lain-lain. MySQL Merupakan database server yang paling sering digunakan dalam pemograman PHP*.(Bahri, 2020)*

### Voluntary Application Server Identification (VAPID)

> VAPID (Voluntary Application Server Identification) adalah skema otentikasi HTTP yang digunakan oleh application server (server pengirim notifikasi) untuk secara sukarela mengidentifikasi dirinya kepada push service dalam konteks Web Push Protocol. (Beverloo, 2017).

### Service Worker

>  Service Worker adalah skrip JavaScript yang dijalankan oleh browser di latar belakang (background), terpisah dari halaman web utama. Fungsinya adalah mengelola komunikasi antara aplikasi web dan jaringan (internet) serta mengatur penyimpanan cache agar situs web bisa tetap berfungsi walaupun tanpa koneksi internet. Dengan kata lain, Service Worker bertindak sebagai perantara antara browser dan server, memungkinkan aplikasi web memiliki kemampuan seperti aplikasi native(Aripin & Somantri, 2021)

### Web Push Notification

> Web Push Notification merupakanpemberitahuan yang dapat dikirim ke pengguna melalui web desktopdan webseluler. Pemberitahuan ini merupakanpesan gaya lansiran yang tampil padasudut kanan atas atau bawah layar desktop, tergantung pada sistem operasi,atau muncul di perangkat seluler dengan cara yang hampir identicdengan Push Notificationyang dikirim dari aplikasi. Web Push Notification dikirimkan didesktop atau layer seluler pengguna kapanpun ketika web browser dijalankan meskipun pengguna membuka halaman web atau tidak(Rahmatulloh et al., 2019).

### Diagram UML

> *Unified Modeling Language* (UML) merupakan sistem arsitektur yang bekerja dalam OOAD *(Object-Oriented Analysis/Design)* dengan satu bahasa yang konsisten untuk menentukan, visualisasi, mengkontruksi dan mendokumentasikan *artifact* (sepotong informasi yang digunakan atau dihasilkan dalam suatu proses rekayasa *software*, dapat berupa model, deksripsi atau *software*) yang terdapat dalam sistem *software*. UML merupakan bahasa pemodelan yang paling sukses dari tiga metode OO (*object oriented*) yang telah ada sebelumnya, yaitu Booch, OMT (*Object Modeling Technique*) dan OOSE *(Object Oriented Software Engineering)*. (Yuniar et al., 2024)
>
> Beberapa Tujuan dari perlunya penggambaran menggunakana Diagram UML diantaranya:

1.  Memberikan model yang siap pakai, bahasa pemodelan visual yang ekspresif untuk mengembangkan dan saling menukar model dengan mudah dan dimengerti secara umum

2.  Memberikan bahasa pemodelan yang bebas dari berbagai bahasa pemrograman dan proses rekayasa. Menyatukan praktik-praktik terbaik yang terdapat dalam pemodelan.

> Pada Implementasi Aplikasi penjualan ini diperlukan beberapa Diagram diantaranya yaitu :

1.  *UseCase* Diagram

> Dalam diagram ini digunakan untuk merepresentasikan hal-hal yang dapat dilakukan oleh aktor, dimana aktor dapat berupa manusia ataupun suatu sistem dalam menyelesaikan sebuah pekerjaan (Heriyanto, 2018). Diagram ini juga merupakan pemodelan untuk kelakuan (*behavior*) sistem yang akan dibuat. Secara kasar *usecase* terdapat fungsi apa saja yang terdapat pada sebuah sistem. Berikut adalah simbol-simbol pada *usecase* diagram :

+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **No** | **Simbol**                                                                         | **Deskripsi**                                                                                                                                                                                                                                              |
+========+====================================================================================+============================================================================================================================================================================================================================================================+
| 1\.    | ![](media/image2.jpeg){width="1.4375in" height="0.8541666666666666in"}             | Fungsionalitas yang disajikan sistem dalam beberapa unit yang saling tukar menukar pesan antarnya dengan aktor. Biasanya *UseCase* dituliskan dengan menggunakan kata kerja diawal di awal frasa nama *usecase.*                                           |
|        |                                                                                    |                                                                                                                                                                                                                                                            |
|        | Gambar 2. UseCase                                                                  |                                                                                                                                                                                                                                                            |
+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **2.** | ![](media/image3.jpeg){width="0.8541666666666666in" height="0.9375in"}             | Manusia, proses atau dapat juga suatu sistem yang berkomunikasi kepada sebuah sistem informasi dimana dibuat di luar sistem informasi yang akan dikembangkan dari sistem itu sendiri. Kata benda adalah frase yang biasa digunakan untuk menuliskan Aktor. |
|        |                                                                                    |                                                                                                                                                                                                                                                            |
|        | Gambar 2. Aktor                                                                    |                                                                                                                                                                                                                                                            |
+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **3.** | ![](media/image4.jpeg){width="1.6979166666666667in" height="0.40625in"}            | Penghubung antara komunikasi aktor dengan *usecase* maupun sebaliknya.                                                                                                                                                                                     |
|        |                                                                                    |                                                                                                                                                                                                                                                            |
|        | Gambar 2. Asosiasi/association                                                     |                                                                                                                                                                                                                                                            |
+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **4.** | ![](media/image5.jpeg){width="1.8333333333333333in" height="0.6145833333333334in"} | Relasi *usecase* tambahan pada sebuah *usecase* yang mana *usecase* yang telah ditambah dapat beroperasi sendiri walaupun tanpa *usecase* tambahan. Seperti penggunaan *inheritance* pada pemrograman berbasis objek.                                      |
|        |                                                                                    |                                                                                                                                                                                                                                                            |
|        | Gambar 2. Ekstensi/Extend                                                          |                                                                                                                                                                                                                                                            |
+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **5.** | ![](media/image6.jpeg){width="1.7708333333333333in" height="0.6354166666666666in"} | Hubungan umum (generalisasi) -- khusus (spesialisasi) antar dua *usecase* yang mana satu fungsi lebih *general* dari lainnya                                                                                                                               |
|        |                                                                                    |                                                                                                                                                                                                                                                            |
|        | Gambar 2. Generalisasi                                                             |                                                                                                                                                                                                                                                            |
+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| **6.** | ![](media/image7.jpeg){width="1.7083333333333333in" height="0.5625in"}             | Hubungan *usecase* tambahan pada suatu *usecase* yang mana usecase yang ditambahkan membutuhkan usecase ini demi dapat mengoperasikan fungsinya.                                                                                                           |
|        |                                                                                    |                                                                                                                                                                                                                                                            |
|        | Gambar 2. Menggunakan/include                                                      |                                                                                                                                                                                                                                                            |
+--------+------------------------------------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

[]{#_Toc219642063 .anchor}Table 2. UseCase Diagram

2.  Activity Diagram

> *Activity* Diagram digunakan untuk penggambaran *workflow* atau alur kerja dari sebuah sistem atau proses bisnis. Perlu diketahui, pada diagram ini penggambaran bukan berdasarkan aktor sehingga aktivitas dapat dilakukan oleh sistem. Dalam *Activity* diagram diperlukan simbol-simbol dalam pembuatannya, diantaranya sebagai berikut :

+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| **No** | **Simbol**                                                                                                                                                   | **Deskripsi**                                                                                                                 |
+========+==============================================================================================================================================================+===============================================================================================================================+
| 1      | Status Awal /*start*                                                                                                                                         | Sebuah awal dari aktivitas suatu sistem pada diagram.                                                                         |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| 2      | Aktivitas /*activity*                                                                                                                                        | Adalah suatu Kegiatan yang dijalankan sebuah sistem. Kata yang digunakan biasanya menggunakan kata kerja.                     |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| 3      | Percabangan*/ Decision*                                                                                                                                      | Percabangan yaitu adanya pilihan aktivitas yang mana dapat memilih satu dari beberapa pilihan.                                |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| 4      | *Swimlane*                                                                                                                                                   | Berguna untuk memecah organisasi bisnis yang memiliki tanggung jawab terhadap kegiatan yang terjadi.                          |
|        |                                                                                                                                                              |                                                                                                                               |
|        | ![A black and white rectangular object AI-generated content may be incorrect.](media/image8.png){width="1.3025798337707786in" height="0.7099901574803149in"} |                                                                                                                               |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| 5      | Status Akhir*/ Finish*                                                                                                                                       | *Finish* atau Status Terakhir dari sistem sebagai penutup akhir dari suatu diagram aktivitas.                                 |
|        |                                                                                                                                                              |                                                                                                                               |
|        | ![](media/image9.png)                                                                                                                                        |                                                                                                                               |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| 6      | ![A diagram of a diagram AI-generated content may be incorrect.](media/image10.png){width="1.6666666666666667in" height="0.9583333333333334in"}              | Penggabungan dimana antara beberapa aktivitas yang terdapat pada diagram, sehingga menjadi suatu aktivitas gabungan sendiri.  |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+
| 7      | ![A black arrow pointing to the right AI-generated content may be incorrect.](media/image11.png){width="1.6356452318460193in" height="0.8855402449693788in"} | Menghubungkan alur dari satu bagian diagram ke bagian lain (misalnya jika diagram besar dan perlu dilanjutkan di tempat lain) |
+--------+--------------------------------------------------------------------------------------------------------------------------------------------------------------+-------------------------------------------------------------------------------------------------------------------------------+

[]{#_Toc218532545 .anchor}Table 2. Activity Diagram

3.  Class Diagram

> Diagram Kelas digunakan untuk menggambarkan struktur sistem dari segi pendefinisian kelas-kelas yang akan dibuat dalam pembangun sistem. Pada setiap Kelas memiliki Atribut dan Metode. Atribut merupakan variabel-variabel yang dimiliki oleh suatu kelas, sedangkan operasi atau metode adalah fungsi-fungsi yang dimiliki oleh suatu kelas (Heriyanto, 2018). Penggambaran struktur sistem juga perlu memerhatikan simbol-simbol yang ada. Berikut simbol-simbol pada diagram kelas:

+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| No   | Simbol                                                                                                                                                      | Deskripsi                                                                                                             |
+======+=============================================================================================================================================================+=======================================================================================================================+
| 1    | Kelas/*class*                                                                                                                                               | Struktur ini terdiri dari nama kelas, atribut serta operasi yang dilakukan kelas tersebut.                            |
|      |                                                                                                                                                             |                                                                                                                       |
|      | ![](media/image12.png)                                                                                                                                      |                                                                                                                       |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| 2    | AntarMuka / *Interface*                                                                                                                                     | Seperti halnya interface pada pemrograman berbasis objek.                                                             |
|      |                                                                                                                                                             |                                                                                                                       |
|      | **nama_interface**                                                                                                                                          |                                                                                                                       |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| 3    | Asosiasi/*Association*                                                                                                                                      | Hubungan antar kelas yang bermakna umum, seringkali diikuti dengan *multiplicity*.                                    |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| 4    | Asosiasi berarah/*directed association*                                                                                                                     | Hubungan dimana sebuah *class* juga digunakan *class* yang lain. Hubungan ini seringkali juga diikuti *multiplicity*. |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| 5    | Generalisasi                                                                                                                                                | Hubungan dimana bermakna Umum-Khusus.                                                                                 |
|      |                                                                                                                                                             |                                                                                                                       |
|      | ![A black line on a white background AI-generated content may be incorrect.](media/image13.png){width="1.4895833333333333in" height="0.6145833333333334in"} |                                                                                                                       |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| 6    | Kebergantungan/*dependency*                                                                                                                                 | Hubungan diaman berarti suatu *class* memiliki ketergantungan terhadap *class* lainnya.                               |
|      |                                                                                                                                                             |                                                                                                                       |
|      | ![](media/image14.png)                                                                                                                                      |                                                                                                                       |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+
| 7    | Agregasi/*aggregation*                                                                                                                                      | Hubungan yangmana sebuah kelas adalah bagian dari kelas lainnya.                                                      |
|      |                                                                                                                                                             |                                                                                                                       |
|      | ![](media/image15.png){width="1.6041666666666667in" height="0.4895833333333333in"}                                                                          |                                                                                                                       |
+------+-------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------+

[]{#_Toc218532546 .anchor}Table 2. Class Diagram

## Kerangka Pemikiran

  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  **Identifikasi Masalah**
  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Penelitian ini dilatarbelakangi oleh inefisiensi operasional pada barbershop modern, khususnya masalah krusial pelanggan tidak hadir (no-show) setelah melakukan reservasi. Fenomena no-show ini mengakibatkan terbuangnya slot layanan (idle time) dan kerugian operasional. Meskipun sistem reservasi berbasis online telah menjadi solusi awal, sistem yang ada belum mampu menyediakan mekanisme notifikasi pengingat yang andal (background notification) dan belum terintegrasi dengan logika otomatisasi status slot layanan yang terancam kedaluwarsa. Ketergantungan pada solusi notifikasi pihak ketiga juga menimbulkan biaya dan kompleksitas.mentasikan menggunakan konsep

  **Tujuan**

  Untuk mengatasi masalah no-show dan idle time, penelitian ini bertujuan membangun sistem reservasi terpusat menggunakan Framework Laravel dengan fokus pada implementasi Web Push Notification Berbasis PWA dengan Protokol VAPID. Tujuan utamanya adalah untuk memastikan notifikasi pengingat dapat terkirim secara andal di latar belakang (melalui PWA) dan terotentikasi secara mandiri (melalui VAPID), yang kemudian diintegrasikan dengan logika otomatisasi backend untuk mengubah status reservasi menjadi \"hangus\", sehingga secara efektif memitigasi kerugian operasional akibat slot layanan yang terbuang

  **Metode**

  Pendekatan yang digunakan adalah Metode Rancang Bangun sistem berbasis web menggunakan Framework Laravel. Solusi teknis diwujudkan melalui implementasi Web Push Notification Berbasis PWA dengan Protokol VAPID. Arsitektur PWA di sisi frontend menjamin notifikasi terkirim secara andal di latar belakang (melalui Service Worker), sementara protokol VAPID digunakan pada backend untuk otentikasi pengiriman notifikasi yang mandiri dan aman. Aspek logika bisnis utama diimplementasikan melalui scheduler Laravel yang berfungsi memvalidasi waktu kedatangan, memicu reminder, dan secara otomatis menetapkan status \"hangus\" pada reservasi yang terlewat.

  **Pengujian**

  Pengujian sistem dilakukan untuk memastikan bahwa sistem yang dikembangkan telah berfungsi sesuai dengan rancangan dan dapat diterima oleh pengguna. Metode pengujian yang digunakan meliputi Black Box Testing untuk memvalidasi fungsionalitas utama sistem, seperti proses reservasi, pengiriman notifikasi pengingat, dan fitur Progressive Web App, serta User Acceptance Testing (UAT) untuk menilai tingkat penerimaan dan kepuasan pengguna terhadap sistem berdasarkan pengalaman penggunaan secara langsung.

  **Hasil**

  Hasil akhir yang diharapkan adalah tersedianya sistem manajemen barbershop yang dilengkapi dengan fitur Web Push Notification Berbasis PWA dengan Protokol VAPID, yang diharapkan mampu menurunkan tingkat kasus no-show dan mengoptimalkan pemanfaatan slot waktu. Sistem ini memberikan kontribusi teknis berupa model implementasi solusi notifikasi yang aman dan skalabel, siap mendukung efisiensi operasional Nusantara Pangkas Rambut.
  -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

[]{#_Toc218532547 .anchor}Table 2. Kerangka Pemikiran

#  METODE PENELITIAN

## Metode Pengumpulan Data

Metode pengumpulan data digunakan untuk memperoleh informasi yang relevan sebagai dasar perancangan dan pengembangan sistem. Teknik pengumpulan data yang digunakan dalam penelitian ini meliputi::

1.  Wawancara

> Wawancara dilakukan secara langsung dengan pemilik barbershop dan staf operasional untuk menggali kebutuhan sistem serta permasalahan yang terjadi dalam proses reservasi dan pelayanan pelanggan. Wawancara difokuskan pada aspek pengelolaan jadwal layanan, permasalahan pelanggan yang terlambat atau tidak hadir (no-show), serta kebutuhan akan sistem pengingat otomatis. Contoh pertanyaan yang diajukan meliputi:

-   Bagaimana alur reservasi pelanggan yang berjalan saat ini dan bagaimana pencatatannya?

-   Berapa lama batas waktu toleransi yang diberikan kepada pelanggan sebelum sebuah jadwal dianggap hangus atau dialokasikan kepada pelanggan lain?

-   Apakah diperlukan sistem pengingat otomatis yang tetap berjalan meskipun aplikasi atau browser dalam keadaan tertutup?

-   Apakah pemilik menginginkan sistem notifikasi mandiri yang efisien dan tidak bergantung pada biaya atau layanan pihak ketiga?

2.  Observasi

> Pada saat melakikan observasi permasalahan utama yang teridentifikasi adalah keterlambatan pelanggan yang berkisar antara 15-20 menit dari waktu reservasi yang disepakati, serta kasus no-show yang terjadi sekitar 6-10 kali per hari. Meskipun slot waktu yang ditinggalkan pelanggan no-show dapat dialihkan kepada pelanggan walk-in,proses ini menciptakan beberapa masalah operasional:
>
> Pertama, capster harus menunggu dalam ketidakpastian selama 15-30 menit untuk memastikan apakah pelanggan booking akan datang atau tidak, yang mengakibatkan waktu tunggu yang tidak produktif dan mengganggu ritme kerja.
>
> Kedua, pelanggan walk-in harus menunggu lebih lama karena prioritas tetap diberikan kepada pelanggan booking hingga batas waktu toleransi tertentu, yang menyebabkan ketidakpuasan dan pengalaman pelayanan yang kurang optimal.
>
> Ketiga, tidak adanya sistem pengingat otomatis menyebabkan tingginya tingkat keterlambatan dan no-show yang sebenarnya dapat dicegah, mengingat banyak pelanggan yang lupa dengan jadwal reservasi mereka.
>
> Selain itu, pencatatan manual sering kali menimbulkan kesalahan data seperti double booking atau kehilangan catatan reservasi, yang memperburuk kualitas pelayanan secara keseluruhan. Kombinasi permasalahan ini menunjukkan perlunya sistem pengingat otomatis yang dapat mengurangi tingkat no-show dan keterlambatan,serta meningkatkan kepastian jadwal bagi capster dan kepuasan pelanggan.

3.  Studi Pustaka

> Metode pengumpulan data ini dilakukan dengan cara mencari, mempelajari, dan mengumpulkan referensi-referensi yang relevan dengan topik penelitian. Sumber data diperoleh dari buku, jurnal ilmiah, prosiding, serta artikel internet terpercaya. Topik yang dipelajari secara mendalam meliputi penggunaan framework Laravel dan ekosistem Laravel Filament sebagai fondasi pengembangan sistem berbasis web. Kajian difokuskan pada konsep Progressive Web App (PWA), Service Worker, Web Push Notification, protokol VAPID, metode Rapid Application Development (RAD), serta metode pengujian User Acceptance Testing (UAT) sebagai landasan teoritis penelitian.

## Analisis Data

Analisis data dilakukan dengan cara mengolah informasi yang diperoleh dari hasil observasi dan wawancara untuk menyusun spesifikasi kebutuhan perangkat lunak (Software Requirements Specification). Dalam penelitian ini, penulis menggunakan pendekatan analisis kebutuhan sistem untuk memetakan masalah ketidakhadiran pelanggan (no-show) ke dalam solusi teknis berbasis otomatisasi. Proses analisis dilakukan dengan mengidentifikasi entitas bisnis, mendefinisikan aturan bisnis (business rules), serta menentukan batasan teknis (constraints). Hasil analisis dikategorikan menjadi dua aspek utama:

### Analisis Kebutuhan Fungsional 

A.  Kebutuhan Fungsional

Kebutuhan fungsional mendeskripsikan fitur spesifik yang harus disediakan sistem untuk mendukung aktivitas operasional dan mitigasi no-show di Nusantara Pangkas Rambut. Berdasarkan hierarki pengguna dan aktor sistem, kebutuhan tersebut dirinci sebagai berikut:

1.  Pelanggan

    a.  Reservasi Mandiri: Kemampuan pelanggan untuk memilih jadwal layanan dan barber melalui antarmuka berbasis web.

    b.  Penerimaan Notifikasi: Fitur untuk menerima Web Push Notification sebagai pengingat jadwal langsung pada perangkat seluler atau desktop melalui Service Worker

2.  Sistem (Automasi Backend)

    a.  Notifikasi Proaktif: Sistem secara otomatis mengirimkan pesan pengingat beberapa menit sebelum waktu reservasi dimulai menggunakan protokol VAPID.

    b.  Otomatisasi Status \"Hangus\": Logika sistem menggunakan Laravel Scheduler untuk memvalidasi kehadiran dan secara otomatis mengubah status reservasi menjadi \"hangus\" jika pelanggan melewati batas toleransi waktu.

    c.  Realokasi Slot: Sistem secara otomatis membebaskan kembali slot waktu yang telah hangus agar dapat digunakan oleh pelanggan lain (walk-in).

3.  Staf (Admin/Kasir)

    a.  Monitoring Antrean: Fitur untuk memantau status reservasi pelanggan secara real-time, baik yang berstatus aktif, selesai, maupun hangus

```{=html}
<!-- -->
```
B.  Analisis Kebutuhan Non Fungsional

> Analisis ini menetapkan batasan kualitas dan standar teknis yang wajib dipenuhi sistem agar mekanisme mitigasi no-show berjalan optimal:

1.  Notification

> Sistem harus menjamin pengiriman notifikasi di latar belakang (background notification) melalui arsitektur PWA, sehingga pesan tetap sampai meskipun aplikasi web sedang tidak dibuka oleh pengguna

2.  Kemandirian Infrastruktur:

> Pengiriman notifikasi wajib menggunakan protokol VAPID untuk memastikan autentikasi mandiri tanpa ketergantungan pada layanan pihak ketiga milik cloud vendor eksterna.

3.  Keamanan (Protokol HTTPS):

> Sebagai syarat wajib arsitektur PWA dan Service Worker, sistem harus berjalan di atas protokol HTTPS untuk menjamin keamanan pertukaran data antara server dan browser

4.  Responsivitas Antarmuka:

> Antarmuka sistem harus dirancang responsif menggunakan prinsip Progressive Web App agar memberikan pengalaman penggunaan yang serupa dengan aplikasi native pada berbagai perangkat seluler pelanggan.

## Metode yang Diusulkan 

Penelitian ini menerapkan metode Rapid Application Development (RAD) dalam pengembangan sistem reservasi Nusantara Pangkas Rambut. Menurut(Sumarto, 2023), RAD merupakan pendekatan pengembangan yang dapat mempercepat waktu penyelesaian aplikasi secara signifikan melalui siklus iteratif yang melibatkan purwarupa (prototyping). Metode ini dipilih karena sangat efektif untuk mengimplementasikan fitur teknis spesifik seperti Web Push Notification dan otomatisasi scheduler yang membutuhkan umpan balik cepat dari pengguna untuk memastikan keandalan notifikasi di berbagai perangkat

![A diagram of process of application development AI-generated content may be incorrect.](media/image16.png){width="4.384027777777778in" height="2.4715277777777778in"}

Gambar 3. Rapid Application Development

Proses pengembangan sistem dilaksanakan melalui empat tahapan siklus hidup RAD sebagaimana diilustrasikan pada Gambar 3.1 Penjelasan rinci mengenai aktivitas teknis pada setiap tahapan adalah sebagai berikut:

### Perancangan Kebutuhan

Tahap awal ini merupakan fase kolaborasi untuk menyepakati batasan lingkup sistem reservasi yang akan dibangun. Fokus utama perencanaan meliputi:

1.  Identifikasi Logika Otomatisasi:

> Menetapkan parameter waktu toleransi kedatangan pelanggan dan pemicu perubahan status reservasi menjadi \"hangus\"

2.  Analisis Infrastruktur PWA

> Menyepakati penggunaan arsitektur Progressive Web App (PWA) guna mendukung fitur notifikasi latar belakang (background notification)

3.  Definisi Aktot

> Memetakan peran pengguna (Super Admin, Admin Cabang, Kasir, dan Pelanggan) beserta kewenangan aksesnya dalam proses reservasi.

### Desain Pengguna

Pada tahap ini, penulis melakukan perancangan sistem dan pembangunan purwarupa (prototyping) secara berulang (iterative) guna menguji pengalaman pengguna dalam menerima notifikasi. Tahapan ini mencakup:

1.  Pemodelan Data

> Merancang skema basis data yang mengakomodasi jadwal layanan, data pelanggan, dan histori status reservasi

2.  High Fidelity Prototyping:

> Membangun antarmuka sistem reservasi menggunakan komponen reaktif agar pengguna dapat langsung mencoba proses booking dan memverifikasi penerimaan Web Push Notification

3.  Desain Skema Notifikasi:

> Merancang alur pengiriman pesan proaktif melalui protokol VAPID untuk memastikan otentikasi mandiri yang aman antara server dan perangkat pelanggan.

### Tahapan Konstruksi

Tahap ini berfokus pada penerjemahan desain ke dalam kode program fungsional dengan aktivitas utama meliputi:

1.  Implementasi PWA dan Service Worker

> Mengembangkan skrip Service Worker untuk menangani push event di latar belakang agar notifikasi tetap terkirim meskipun browser ditutup

2.  Otomatisasi Backend dengan Laravel Scheduler

> Menulis kode program pada scheduler Laravel untuk memvalidasi kehadiran pelanggan dan mengeksekusi pengubahan status menjadi \"hangus\" secara otomatis.

3.  Integrasi Protokol VAPID

> Mengonfigurasi kunci otentikasi mandiri pada sisi server untuk mengirimkan Web Push tanpa ketergantungan pada layanan pihak ketiga.

### Pengalihan

Fase transisi sistem ke lingkungan operasional nyata di Nusantara Pangkas Rambut. Kegiatan meliputi:

1.  Pengujian Akhir

> Pengujian fungsional dilakukan secara internal oleh peneliti sebelum system diserahkan kepada pengguna akhir. Pengujian ini bersifat verifikasi teknis untuk memastikan seluruh fitur sistem, termasuk proses reservasi dan pengiriman notifikasi pengingat, berfungsi sesuai dengan rancangan tanpa kesalahan kritis.Pengujian penerimaan pengguna (User Acceptance Testing) akan dilakukan sebagai tahap terpisah setelah sistem mulai digunakan oleh pengguna akhir.

2.  Persiapan Lingkungan Sistem

> Pada tahap ini dilakukan konfigurasi akhir sistem, termasuk pengaturan server, basis data, serta konfigurasi Progressive Web App dan Web Push Notification menggunakan protokol VAPID. Persiapan ini bertujuan agar sistem dapat diakses dan digunakan secara stabil oleh pengguna.

3.  Pelatihan Pengguna

> Setelah sistem dinyatakan siap secara fungsional, sistem diperkenalkan kepada pengguna akhir untuk digunakan sesuai dengan perannya masing-masing. Tahap ini menjadi penghubung antara proses pengembangan sistem dan tahap pengujian penerimaan pengguna (User Acceptance Testing).

## Metode Pengujian

Metode pengujian dalam penelitian ini bertujuan untuk memastikan bahwa sistem reservasi digital berbasis Progressive Web App (PWA) dengan fitur Web Push Notification menggunakan protokol VAPID dapat diterima dan digunakan secara efektif oleh pengguna. Mengingat fokus penelitian ini adalah pada tingkat penerimaan pengguna serta manfaat sistem dalam mengurangi permasalahan keterlambatan dan ketidakhadiran pelanggan (no-show), maka metode pengujian yang digunakan adalah User Acceptance Testing (UAT).

Pengujian fungsional dasar terhadap sistem dilakukan secara internal oleh peneliti untuk memastikan seluruh fitur berjalan sesuai dengan rancangan sebelum dilakukan pengujian penerimaan pengguna. Selanjutnya, pengujian difokuskan pada UAT untuk menilai apakah sistem telah memenuhi kebutuhan dan ekspektasi pengguna akhir.

### Black Box Testing

Metode Black Box Testing digunakan untuk memvalidasi fungsionalitas sistem dengan cara mengamati kesesuaian antara masukan (input) dan keluaran (output) tanpa memperhatikan struktur kode program atau logika internal yang digunakan. Pendekatan ini dipilih karena efektif dalam memastikan bahwa fitur-fitur utama sistem berjalan sesuai dengan kebutuhan pengguna dan bebas dari kesalahan fungsional pada saat digunakan secara nyata.

Pada penelitian ini, Black Box Testing dilakukan oleh peneliti dengan menggunakan skenario pengujian yang difokuskan pada tiga aspek utama sistem reservasi barbershop berbasis Progressive Web App (PWA), yaitu sebagai berikut:

1.  Validasi Proses Reservasi (Functional Flow Testing)

> Pengujian ini bertujuan untuk memastikan bahwa alur proses reservasi berjalan dengan benar, mulai dari pelanggan memilih jadwal layanan, melakukan pemesanan, hingga sistem menyimpan data reservasi ke dalam basis data. Skenario uji dilakukan dengan memberikan input data reservasi yang berbeda untuk memastikan sistem mampu menangani pemesanan secara konsisten dan menghasilkan keluaran berupa konfirmasi reservasi yang sesuai.

2.  Validasi Pengiriman Notifikasi Pengingat (Notification Testing)

> Pengujian ini difokuskan pada mekanisme pengiriman notifikasi pengingat kepada pelanggan. Skenario uji dilakukan dengan memastikan bahwa notifikasi dikirimkan sesuai dengan jadwal reservasi yang telah ditentukan melalui Web Push Notification menggunakan protokol VAPID. Pengujian ini bertujuan untuk memastikan bahwa notifikasi dapat diterima oleh pengguna meskipun aplikasi web tidak sedang dibuka, sesuai dengan karakteristik Progressive Web App.

3.  Validasi Fungsionalitas Progressive Web App (PWA Testing)

> Pengujian ini dilakukan untuk memastikan fitur-fitur utama PWA berjalan dengan baik, seperti instalasi aplikasi ke perangkat pengguna, penggunaan service worker, serta kemampuan sistem untuk tetap menampilkan halaman yang telah di-cache. Pengujian ini bertujuan memastikan sistem dapat memberikan pengalaman penggunaan yang stabil dan konsisten bagi pelanggan.

### User Acceptance Testing (UAT) 

Setelah sistem dinyatakan berjalan dengan baik secara teknis melalui pengujian internal, tahap selanjutnya adalah User Acceptance Testing (UAT). UAT dilakukan sebagai bentuk evaluasi akhir untuk memastikan bahwa sistem yang dikembangkan telah sesuai dengan kebutuhan pengguna dan siap digunakan dalam kegiatan operasional sehari-hari. Menurut (Tong et al., 2022), User Acceptance Testing merupakan proses pengujian yang berfokus pada penilaian pengguna akhir untuk menentukan apakah suatu sistem telah memenuhi kebutuhan spesifik pengguna dan dapat diterima untuk diimplementasikan.

Dalam penelitian ini, User Acceptance Testing (UAT) melibatkan dua kelompok pengguna, yaitu pengguna internal dan pengguna eksternal. Pengguna internal terdiri dari Pemilik (Super Admin) dan Manajer Operasional Cabang yang berperan dalam pengelolaan data reservasi dan jadwal layanan. Sementara itu, pengguna eksternal adalah pelanggan yang menggunakan sistem untuk melakukan reservasi dan menerima notifikasi pengingat. Pelibatan kedua kelompok pengguna tersebut bertujuan untuk memperoleh penilaian yang objektif berdasarkan pengalaman nyata dalam mengoperasikan sistem, baik dari sisi pengelolaan sistem maupun dari sisi penerimaan dan kejelasan notifikasi pengingat reservasi.

Pelaksanaan UAT dilakukan setelah sistem dinyatakan berjalan dengan baik secara teknis melalui pengujian internal. Prosedur pengujian UAT dalam penelitian ini dilaksanakan melalui beberapa tahapan sebagai berikut:

1.  Demonstrasi dan Uji Coba Sistem

> Peneliti memberikan penjelasan singkat mengenai fungsi dan alur penggunaan sistem kepada responden. Selanjutnya, responden diminta untuk mengoperasikan sistem sesuai dengan peran masing-masing, seperti melakukan pengelolaan reservasi bagi pengguna internal dan melakukan reservasi serta menerima notifikasi pengingat bagi pelanggan. Tahap ini bertujuan untuk memastikan bahwa sistem dapat digunakan sesuai dengan kebutuhan nyata pengguna.

2.  Pengisian Kuesioner Penilaian

> Setelah melakukan uji coba sistem, responden diminta untuk mengisi kuesioner penilaian yang disusun menggunakan skala Likert lima tingkat (1--5). Kuesioner ini mencakup beberapa aspek penilaian, antara lain kemudahan penggunaan sistem, kejelasan informasi yang ditampilkan, keandalan notifikasi pengingat, serta kesesuaian sistem dengan kebutuhan pengguna.

3.  Analisis Tingkat Penerimaan Pengguna

> Data yang diperoleh dari kuesioner kemudian dianalisis untuk mengetahui tingkat penerimaan pengguna terhadap sistem yang dikembangkan. Sistem dinyatakan dapat diterima dan layak digunakan apabila nilai rata-rata tingkat persetujuan responden mencapai ambang batas minimal yang telah ditetapkan, misalnya lebih dari 80%. Hasil analisis ini digunakan sebagai dasar evaluasi keberhasilan sistem dan menjadi bahan pembahasan pada bab selanjutnya.

# DAFTAR PUSTAKA {#daftar-pustaka .unnumbered}

Almaarij, M. A. F., Mansyuri, U., & Arief, R. (2025). Design of Reservation and Customer Management Information System at Polka Barbershop Using Laravel Framework. *Brilliance: Research of Artificial Intelligence*, *5*(1), 500--509. https://doi.org/10.47709/brilliance.v5i1.5914

Andi Juandi, M. N. A., Author, A. S. Y. I., & Author, P. (2025). IMPLEMENTASI FRAMEWORK LARAVEL PADA APLIKASI PEMESANAN BARBERSHOP BERBASIS WEB (STUDI KASUS: MAIDEN BARBERROCK). *Jurnal Informatika Dan Teknik Elektro Terapan*, *13*(3S1). https://doi.org/10.23960/jitet.v13i3S1.7520

Aripin, S., & Somantri, S. (2021). Implementasi Progressive Web Apps (PWA) pada Repository E-Portofolio Mahasiswa. *Jurnal Eksplora Informatika*, *10*(2), 148--158. https://doi.org/10.30864/eksplora.v10i2.486

Bahri, S. (2020). RANCANG BANGUN SISTEM INFORMASI BERBASIS WEB PADA TEACHING FACTORY BAKERY SMK PUTRA ANDA BINJAI. *Informatika : Fakultas Sains Dan Teknologi*, *8*(3).

Beverloo, P. (2017). Internet Engineering Task Force (IETF) M. Thomson Request for Comments: 8292 Mozilla Category: Standards Track. *Internet Engineering Task Force (IETF)*. https://trustee.ietf.org/license-info

Bima Pratama, A., & Triawan, A. (2024). TeknoIS: Jurnal Ilmiah Teknologi Informasi dan Sains \[225\] Penerapan Push Notification Menggunakan Representational State Transfer (REST) Untuk Informasi APD Petugas Damkar. *Jurnal Ilmiah Teknologi-Informasi &Sains*, *14*, 225--235. https://doi.org/10.36350/jbs.v14i2

Devyanti, K. N., Yuanda, R., Rudiansah, C., Lubis, M. A., Wicaksono, A., Nasir, M., Rekayasa, T., Lunak, P., & Vokasi, S. (2025). Development of a Web-Based Reservation System for Cakra Skin Beauty Using the Waterfall Method Informasi Artikel ABSTRAK. *Journal of Artificial Intelligence and Software Engineering*, *5*(3), 890--900. https://doi.org/10.30811/jaise.v5i3.7032

Fadli, M. A., Naufal, A., & Suartana, I. M. (2024). Penerapan Push Notification Booking Perlengkapan Outdoor Menggunakan Firebase Cloud Messaging. *Journal of Informatics and Computer Science*, *06*.

Febrian, J., & Ichwani, A. (n.d.). Rancang Bangun Aplikasi Booking Online Layanan Potongan Rambut Berbasis Website Menggunakan REST API. *IKRAITH-INFORMATIKA*. https://doi.org/10.37817/ikraith-informatika.v9i3

Gavilan, D., & Martinez-Navarro, G. (2022). Exploring user's experience of push notifications: a grounded theory approach. *Qualitative Market Research*, *25*(2), 233--255. https://doi.org/10.1108/QMR-05-2021-0061

Haidar, A., Mukaromah, M., Bukhari, I. A., & Susena, E. (2025). Rancangan Sistem Informasi Reservasi Dan Pemasaran Barbershop Berbasis Web Untuk Meningkatkan Efisiensi Operasional. *Jurnal Sains Dan Teknologi Informasi*, 62--71. https://doi.org/10.62951/switch.v3i4.536

Handika Siregar, Y., Nainggolan, M., Ahmad, J. J., Kisaran, Y., & Utara, S. (2018). SISTEM INFORMASI GEOGRAFIS PEMETAAN LOKASI BENCANA ALAM DI SUMATERA UTARA BERBASIS WEB. *Jurnal Teknologi Informasi*, *2*(2).

Hanifan, H., & Fajri, I. N. (2024). ANALISIS DAN IMPLEMENTASI PROGRESSIVE WEB APP (PWA) SERTA FITUR NOTIFIKASI PADA SISTEM INFORMASI PENDAFTARAN VOLUNTEER VOLHUB. *Information System Journal*, *7*(02), 104--117. https://doi.org/10.24076/infosjournal.2024v7i02.1986

Heriyanto, Y. (2018). Perancangan Sistem Informasi Rental Mobil Berbasis Web Pada PT.APM Rent Car. *Jurnal Intra-Tech*, *2*(2), 64--77.

Herman, F. (2023). PROGRESSIVE WEB APPS\_ PENGEMBANGAN DAN STUDI PENERIMAAN PADA MAHASISWA INDONESIA MENGGUNAKAN SCRUMDAN UTAUT. *Jurnal Teknologi Terpadu*, *Vol. 9*.

Imron, M., Sutikno, G. R., Dazki, I. N., Amikom, U., Fakultas, P., Komputer, I., Let, J., Pol, J., Depan, S., Purwokerto, S., & Utara, P. (2020). Implementasi Push Notification Pada Sistem Peminjaman Sarana dan Prasarana Berbasis Website. *JURNAL INFORMATIKA*, *7*(2), 174--182. http://ejournal.bsi.ac.id/ejurnal/index.php/ji

Kaniya, I. A., Paramitha, P., Made Wiharta, D., Made, I., Suyadnya, A., Raya, J., Unud, K., Jimbaran, B., & Selatan, K. (2022). *PERANCANGAN DAN IMPLEMENTASI RESTFUL API PADA SISTEM INFORMASI MANAJEMEN DOSEN UNIVERSITAS UDAYANA* (Vol. 9, Number 3).

Rahmatulloh, A., Rachman, A. N., & Anwar, F. (2019). IMPLEMENTASI WEB PUSH NOTIFICATION PADA SISTEM INFORMASI MANAJEMEN ARSIP MENGGUNAKAN PUSHJS. *Jurnal Teknologi Informasi Dan Ilmu Komputer (JTIIK)*, *6*(3), 337--334. https://doi.org/10.25126/jtiik.20196936

Sains, J., Tekonologi, dan, Surya Ningsih, K., Jamilah Aruan, N., Taufik Al Afkari Siahaan, A., Kunci, K., & Tamu, B. (2022). Yayasan Insan Cipta Medan APLIKASI BUKU TAMU MENGGUNAKAN FITUR KAMERA DAN AJAX BERBASIS WEBSITE PADA KANTOR DISPORA KOTA MEDAN. *SITek: Jurnal Sains, Informatika, Dan Tekonologi*.

Sumarto, M. A. (2023). Analisis dan Perancangan Aplikasi Point of Sale (POS) untuk Usaha Mikro, Kecil, dan Menengah (UMKM) dengan Metode Rapid Application Development (RAD). *Jurnal Studi Komunikasi Dan Media*, *27*(1), 17--34. https://doi.org/10.17933/jskm.2023.5115

Tong, R. T. Y., Yuan, Y. K., Dong, N. W., & Ramasamy, R. K. (2022). A Review: Methods of Acceptance Testing. *Faculty of Computing and Informatics*, 76--86. https://doi.org/10.2991/978-94-6463-080-0_7

Trianasari, A., & Debataraja, B. F. (2020). Sistem Reservasi pada Mores Barbershop berbasis Web di Jatiwarna-Bekasi. In *Jurnal Esensi Infokom* (Vol. 4, Number 1).

 
