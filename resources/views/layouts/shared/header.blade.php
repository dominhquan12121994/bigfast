<style>
    #count_notification {
        position: absolute;
        left: 50%;
        bottom: 75%;
    }

    #box {
        width: 400px;
        padding: 0px;
    }

    .notification_container {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .message {
        padding: 5px;
        background-color: #eee;
        border-radius: 5px;
        width: 100%;
    }

    #box .dropdown-item:hover {
        background-color: white;
    }

    #box .dropdown-item:hover .message {
        background-color: rgba(255,0,0,0.7);
        color: white !important;
    }

    #box .dropdown-item:hover span {
        color: white !important;
    }

    #box .dropdown-item span {
        white-space: pre-wrap;
    }

    #box .dropdown-item.text {
        padding: 2.5px 5px;
    }

    .notification_text {
        cursor: pointer;
        text-decoration: none !important;
        text-align: center;
    }

    .ringing_bell {
        -webkit-animation: ring 3s .7s ease-in-out infinite;
        -webkit-transform-origin: 50% 4px;
        -moz-animation: ring 3s .7s ease-in-out infinite;
        -moz-transform-origin: 50% 4px;
        animation: ring 3s .7s ease-in-out infinite;
        transform-origin: 50% 4px;
    }

    @keyframes ring{0%{transform:rotate(0)}1%{transform:rotate(30deg)}3%{transform:rotate(-28deg)}5%{transform:rotate(34deg)}7%{transform:rotate(-32deg)}9%{transform:rotate(30deg)}11%{transform:rotate(-28deg)}13%{transform:rotate(26deg)}15%{transform:rotate(-24deg)}17%{transform:rotate(22deg)}19%{transform:rotate(-20deg)}21%{transform:rotate(18deg)}23%{transform:rotate(-16deg)}25%{transform:rotate(14deg)}27%{transform:rotate(-12deg)}29%{transform:rotate(10deg)}31%{transform:rotate(-8deg)}33%{transform:rotate(6deg)}35%{transform:rotate(-4deg)}37%{transform:rotate(2deg)}39%{transform:rotate(-1deg)}41%{transform:rotate(1deg)}43%{transform:rotate(0)}100%{transform:rotate(0)}}

    .modal-backdrop {
        display: none;
    }
</style>

<div class="c-wrapper">
    <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
        <button class="c-header-toggler c-class-toggler d-lg-none mr-auto" type="button" data-target="#sidebar"
                data-class="c-sidebar-show"><span class="c-header-toggler-icon"></span></button>
        <a class="c-header-brand d-sm-none" href="#">
            <img class="c-header-brand" src="/assets/img/favicon.png" width="97" height="46" alt="Bigfast Logo">
        </a>
        <button class="c-header-toggler c-class-toggler ml-3 d-md-down-none" type="button" data-target="#sidebar"
                data-class="c-sidebar-minimized" responsive="true"><span class="c-header-toggler-icon"></span></button>
        <?php
        use App\Modules\Systems\MenuBuilder\FreelyPositionedMenus;
        if (isset($appMenus['top menu'])) {
            FreelyPositionedMenus::render($appMenus['top menu'], 'c-header-', 'd-md-down-none');
        }
        ?>
        <ul class="c-header-nav ml-auto mr-4">
            @if($isShipper)
            <li class="c-header-nav-item dropdown d-md-down-none mx-2" id="notification_item">
                <a class="notification_text" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <span id="notification_bell" class="bell fa fa-bell {{ $notifications['unread_notification'] ? 'ringing_bell' : '' }}"></span>
                    <span id="count_notification"
                          class="badge badge-pill badge-danger">{{ $notifications['unread_notification'] ? $notifications['unread_notification'] : '' }}</span>
                    <div id="box" class="dropdown-menu dropdown-menu-right dropdown-menu-lg mt-2 notifications">
                        <div class="dropdown-header bg-light">
                            <strong>Bạn có <span id="count_notification_inside" class="text-warning">{{ number_format($notifications['unread_notification']) }}</span> thông báo mới</strong>
                        </div>
                        <div class="notification_container">
                            <a class="notification_text" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>
                            @foreach($notifications['five_notifications'] as $notification)
                                <div class="notifications-item">
                                    @if($notification->notification->link || strlen($notification->notification->content) > 150)
                                        <a class="dropdown-item text" style="cursor: pointer">
                                            <div class="message" data-toggle="modal" data-target="#modal_{{ $notification->notification_id }}">
                                                <input type="hidden" value="{{ $notification->notification_id }}">
                                                <div><b class="text-warning">Admin</b><small class="text-muted float-right mt-1">{{ date('d-m-Y H:i', strtotime($notification->notification->created_at)) }}</small></div>
                                                <div class=""><span class="text-justify {{ $notification->is_read ? 'text-dark' : 'text-success' }}">{{ strlen($notification->notification->content) > 150 ? substr($notification->notification->content, 0, strrpos(substr($notification->notification->content, 0, 150), " ")) . "..." : $notification->notification->content }}</span></div>
                                            </div>
                                        </a>
                                    @else
                                        <a class="dropdown-item text" style="cursor: pointer">
                                            <div class="message">
                                                <input type="hidden" value="{{ $notification->notification_id }}">
                                                <div><b class="text-warning">Admin</b><small class="text-muted float-right mt-1">{{ date('d-m-Y H:i', strtotime($notification->notification->created_at)) }}</small></div>
                                                <div class=""><span class="text-justify {{ $notification->is_read ? 'text-dark' : 'text-success' }}">{{ strlen($notification->notification->content) > 150 ? substr($notification->notification->content, 0, strrpos(substr($notification->notification->content, 0, 150), " ")) . "..." : $notification->notification->content }}</span></div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <a class="dropdown-item justify-content-center border-top" href="{{ route('admin.user-notification.index') }}"><strong>Xem tất cả thông báo</strong></a>
                    </div>
                </a>
            </li>
            @endif
            <li class="c-header-nav-item d-md-down-none mx-2">
                <b>Xin chào! {{ $currentUser->name }}</b>
            </li>
            <li class="c-header-nav-item dropdown">
                <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="c-avatar">
                        <i class="cil-menu"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right pt-0">
                    <a class="dropdown-item" href="{{ route('admin.change.password') }}">
                        <svg class="c-icon mr-2">
                        <use xlink:href="{{ env('APP_URL', '') }}/icons/sprites/free.svg#cil-lock-unlocked"></use>
                        </svg> Đổi mật khẩu
                    </a>
                    <form action="/admin/logout" method="POST">
                        @csrf 
                        <button class="dropdown-item" type="submit" data-toggle="tooltip" data-placement="bottom"
                                class="btn btn-ghost-dark btn-block" title="Đăng xuất">
                            <svg class="c-icon mr-2">
                                <use xlink:href="{{ env('APP_URL', '') }}/icons/sprites/free.svg#cil-account-logout"></use>
                            </svg> Đăng xuất
                        </button>
                    </form>
                </div>
            </li>
        </ul>
        <div>
            @foreach($notifications['five_notifications'] as $notification)
                @if(strlen($notification->notification->content) > 150 || $notification->notification->link)
                    <div class="modal" id="modal_{{ $notification->notification_id }}">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center">Chi tiết thông báo</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $notification->notification->content }}</p>
                                </div>
                                @if($notification->notification->link)
                                    <div class="modal-footer">
                                        <a class="btn btn-success see_detail" href="{{ $notification->notification->link }}" target="_blank">Xem chi tiết</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="c-subheader px-3">
            <ol class="breadcrumb border-0 m-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <?php $segments = ''; ?>
                @for($i = 1; $i <= count(Request::segments()); $i++)
                    <?php $segments .= '/' . Request::segment($i); ?>
                    @if(isset(OrderConstant::breadcrumb[Request::segment($i)]))
                        @if($i < count(Request::segments()))
                            <li class="breadcrumb-item">{{ \App\Modules\Orders\Constants\OrderConstant::breadcrumb[Request::segment($i)] }}</li>
                        @else
                            <li class="breadcrumb-item active">{{ \App\Modules\Orders\Constants\OrderConstant::breadcrumb[Request::segment($i)] }}</li>
                        @endif
                    @else
                        @if($i < count(Request::segments()))
                            <li class="breadcrumb-item">{{ Request::segment($i) }}</li>
                        @else
                            <li class="breadcrumb-item active">{{ Request::segment($i) }}</li>
                        @endif
                    @endif
                @endfor
            </ol>
        </div>
    </header>

    {{--    // script firebase--------------------------------------------------}}
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <script src="{{ asset('/js/firebase.js') }}"></script>
    {{--    // ...script firebase--------------------------------------------------}}

    <script>
        var unreadNotification = parseInt('{{ $notifications["unread_notification"] }}');

        firebase.initializeApp({
            apiKey: '{{ config('firebase.init.apiKey') }}',
            authDomain: '{{ config('firebase.init.authDomain') }}',
            projectId: '{{ config('firebase.init.projectId') }}',
            storageBucket: '{{ config('firebase.init.storageBucket') }}',
            messagingSenderId: '{{ config('firebase.init.messagingSenderId') }}',
            appId: '{{ config('firebase.init.appId') }}',
            measurementId: '{{ config('firebase.init.measurementId') }}',
        });

        const messaging = firebase.messaging();
        messaging.onMessage((payload) => {
            if (payload.data.id == "-1") {
                $('form[action="/admin/logout"]')[0].submit();
            }

            unreadNotification++;
            if (payload.data.id == "-1") {
                $('form[action="/logout"]')[0].submit();
            }
            document.getElementById('count_notification').innerHTML = unreadNotification;
            document.getElementById('count_notification_inside').innerHTML = unreadNotification;
            if (unreadNotification == 0) {
                $('#notification_bell').removeClass('ringing_bell');
            } else {
                $('#notification_bell').addClass('ringing_bell');
            }
            newNotification = '';
            let shortContent = payload.data.content.length > 150 ? payload.data.content.substr(0, payload.data.content.substr(0, 150).lastIndexOf(" ")) + "..." : payload.data.content;
            if (payload.data.link || payload.data.content.length > 150) {
                newNotification = `
                    <div class="notifications-item">
                        <a class="notification_text" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>
                        <a class="dropdown-item text" style="cursor: pointer">
                            <div class="message" data-toggle="modal" data-target="#modal_` + payload.data.id + `">
                                <input type="hidden" value="` + payload.data.id + `">
                                <input id="link" type="hidden" value="` + payload.data.link + `">
                                <div><b class="text-warning">Admin</b><small class="text-muted float-right mt-1">` + payload.data.date + `</small></div>
                                <div class="">
                                    <span class="text-success text-justify">` + shortContent + `</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="modal" id="modal_` + payload.data.id + `">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center">Chi tiết thông báo</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="modal-body">
                                    <p>` + payload.data.content + `</p>
                                </div>`;
                if (payload.data.link) {
                    newNotification += `
                        <div class="modal-footer">
                            <a class="btn btn-success" href="` + payload.data.link + `" target="_blank">Xem chi tiết</a>
                        </div>
                    `;
                }
                newNotification += `</div></div></div>`;
            } else {
                newNotification = `
                    <div class="notifications-item">
                        <a class="notification_text" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>
                        <a class="dropdown-item text">
                            <div class="message">
                                <input type="hidden" value="` + payload.data.id + `">
                                <div><b class="text-warning">Admin</b><small class="text-muted float-right mt-1">` + payload.data.date + `</small></div>
                                <div class="">
                                    <span class="text-success text-justify">` + shortContent + `</span>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            }
            $('.notification_container').prepend(newNotification);

            $('.notifications-item').click(function (e) {
                readNotification(this);
            });
        });

        function readNotification(element) {
            console.log(element);
            if ($(element).find('span').hasClass('text-success')) {
                unreadNotification--;
                document.getElementById('count_notification_inside').innerHTML = unreadNotification;
                if (unreadNotification == 0) {
                    $('#notification_bell').removeClass('ringing_bell');
                } else {
                    $('#notification_bell').addClass('ringing_bell');
                }
                $(element).find('span').removeClass('text-success');
                $(element).find('span').addClass('text-dark');
                let notificationId = $(element).find('input')[0].value;
                let arrNotificationId = [notificationId];
                $.ajaxSetup({
                    headers: {
                        "_token": "{{ csrf_token() }}",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Authorization': 'Bearer {{ $currentUser->passport_token }}'
                    }
                });
                $.ajax({
                    url: '{{ route("api.set-user-notification-read") }}',
                    type: 'PUT',
                    data: {
                        arr_notification_id: arrNotificationId
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        console.log(response);
                        if (unreadNotification == 0) {
                            document.getElementById('count_notification').innerHTML = '';
                            $('#notification_bell').removeClass('ringing_bell');
                        } else {
                            document.getElementById('count_notification').innerHTML = unreadNotification;
                            $('#notification_bell').addClass('ringing_bell');
                        }
                        $(element).hide(300);
                    },
                    error: function (err) {
                        console.log('thay doi trang thai thong bao that bai: ' + err);
                    },
                });
            }
        }

        $('.notifications-item').click(function (event) {
            readNotification($(this)[0]);
        });

        $('body').on("click", ".dropdown-menu", function (e) {
            e.stopPropagation();
        });

        $('.see_detail').click(function () {
            $(this).parentsUntil('.modal.show').hide();
        });
    </script>
