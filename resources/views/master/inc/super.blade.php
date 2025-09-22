<ul class="nav">
    <li class="nav-item">
        <a href="{{ route('back.dashboard') }}">
            <i class="fas fa-home"></i>
            <p>{{ __('Dashboard') }}</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('back.category.index') }}">
          <i class="fas fa-list-alt"></i>
          <p>{{ __('Manage Categories') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#items">
            <i class="fab fa-product-hunt"></i>
            <p>{{ __('Manage Products') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="items">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.brand.index') }}">
                        <span class="sub-item">{{ __('Brands') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.add') }}">
                        <span class="sub-item">{{ __('Add Product') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.index') }}">
                        <span class="sub-item">{{ __('All Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.stock.out') }}">
                        <span class="sub-item">{{ __('Stock Out Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.bulk.product.index') }}">
                        <span class="sub-item">{{ __('CSV Import & Export') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.review.index') }}">
                      <span class="sub-item">{{ __('Product Reviews') }}</span></a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item {{ request()->is('orders/*') ? 'submenu' : '' }}">
        <a data-toggle="collapse" href="#order">
            <i class="fab fa-first-order"></i>
            <p>{{ __('Manage Orders') }} </p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="order">
            <ul class="nav nav-collapse">
                <li class="{{!request()->input('type') && request()->is('admin/orders')  ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index') }}">
                        <span class="sub-item">{{ __('All Orders') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'Pending' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Pending' }}">
                        <span class="sub-item">{{ __('Pending Orders') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'In Progress' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'In Progress' }}">
                        <span class="sub-item">{{ __('Progress Orders') }}</span>
                    </a>
                </li>

                <li class="{{request()->input('type') == 'Delivered' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Delivered' }}">
                        <span class="sub-item">{{ __('Delivered Orders') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'Canceled' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Canceled' }}">
                        <span class="sub-item">{{ __('Canceled Orders') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('back.code.index') }}">
          <i class="fas fa-newspaper"></i>
          <p>{{ __('Set Coupons') }}</p></a>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#content">
            <i class="fas fa-tasks"></i>
            <p>{{ __('Manage Site') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="content">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.setting.general') }}">
                        <span class="sub-item">{{ __('General Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.email') }}">
                        <span class="sub-item">{{ __('Email Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.sms') }}">
                        <span class="sub-item">{{ __('SMS Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.maintainance') }}">
                      <span class="sub-item">{{ __('Maintainance') }}</span></a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('admin.sitemap.index') }}">
                      <span class="sub-item">{{ __('Sitemap') }}</span></a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.language.index') }}">
                      <span class="sub-item">{{ __('Language') }}</span></a>
                </li>
            </ul>
        </div>
    </li>


    <li class="nav-item">
        <a data-toggle="collapse" href="#user">
            <i class="far fa-user"></i>
            <p>{{ __('System User') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="user">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.role.index') }}">
                        <span class="sub-item">{{ __('Role') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.staff.index') }}">
                        <span class="sub-item">{{ __('System User') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('back.cache.clear') }}">
            <i class="fas fa-broom"></i>
            <p>{{ __('Cache Clear') }}</p>
        </a>
    </li>

</ul>
