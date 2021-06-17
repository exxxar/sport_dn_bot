<section class="chopcafe_contact chopcafe_contact_1 section_padding">
    <div class="container">
        <div class="contact_information_area">
            @if (session('status'))
                <div class="row">
                    <div class="col-12">

                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>

                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-lg-4">
                    <div class="contact_info_box d-flex align-items-center wow slideInUp" style="visibility: visible;">
                        <div class="contact_icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact_info">
                            <a href="tel:+38071-383-07-41">+38071-383-07-41</a>
                        </div>
                    </div>
                    <div class="contact_info_box  d-flex align-items-center wow slideInUp" style="visibility: visible;">
                        <div class="contact_icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact_info">
                            <p>Набережная 153а, Донецк</p>
                        </div>
                    </div>
                    <div class="contact_info_box d-flex align-items-center wow slideInUp" style="visibility: visible;">
                        <div class="contact_icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact_info">
                            <a href="https://t.me/isushibot">t.me/isushibot</a>
                            <a href="mailto:isushi@gmail.com">isushi@gmail.com</a>
                        </div>
                    </div>
                    <div class="social_widget_box wow slideInUp" style="visibility: visible;">
                        <h4>Подпишись на нас</h4>
                        <ul class="social_widget">
                            <li><a href="https://www.facebook.com/profile.php?id=100040447323596" class="facebook"><i
                                            class="fab fa-facebook-f"></i></a></li>
                            <li><a href="https://www.instagram.com/isushi_dn/" target="_blank" class="instagram"><i
                                            class="fab fa-instagram"></i></a></li>
                            <li><a href="https://vk.com/isushi_dn" class="vkontakte"><i class="fab fa-vk"></i></a></li>
                            <li><a href="https://t.me/isushibot" target="_blank" class="telegram"><i class="fab fa-telegram"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="contact_box">
                        <iframe src="https://yandex.ru/map-widget/v1/-/CKU170YW" style="width:100%;height:100vh;padding: 10px;box-sizing: border-box;"
                                frameborder="0" allowfullscreen="true"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="chopcafe_contact">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="chopcafe_title chopcafe_title_1 text-center">
                        <h2>Обратная <span>связь</span></h2>
                        <div class="title_divider">
                            <i class="flaticon-fork-and-knife-in-cross title_fork"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chopcafe_form">

                <form action="{{url('/contacts')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form_group">
                                <input type="text" class="form_control" name="name" placeholder="Ваше имя" required="">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form_group">
                                <input type="email" class="form_control" placeholder="Почта" name="mail" required="">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form_group">
                                <input type="text" class="form_control" placeholder="Заголовок" name="title"
                                       required="">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form_group">
                                <textarea class="form_control" placeholder="Сообщение" name="message"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form_button text-center">
                                <button class="chopcafe_btn form_btn">Отправить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
