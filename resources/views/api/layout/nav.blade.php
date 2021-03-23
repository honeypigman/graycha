<!--
    Title : Header Layout 
    Date : 2021.02.10
    - https://feathericons.com/
-->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>K-AirSpec</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item @if(Request::segment(2)=='SCOA0901') bg-warning @endif">
                <a class="nav-link" aria-current="page" href="/api/SCOA0901">
                <span data-feather="chevrons-right"></span>
                측정소정보
                </a>
            </li>
        </ul>

        <ul class="nav flex-column">
            <li class="nav-item @if(Request::segment(2)=='SCOA0902') bg-warning @endif">
                <a class="nav-link" aria-current="page" href="/api/SCOA0902">
                <span data-feather="chevrons-right"></span>
                대기오염정보
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Management</span>
        </h6>

        <ul class="nav flex-column">
            <li class="nav-item @if(Request::segment(2)=='board') bg-warning @endif">
                <a class="nav-link" aria-current="page" href="/board/SCOA0902">
                <span data-feather="database"></span>
                데이터 관리
                </a>
            </li>
        </ul>

        <!-- <ul class="nav flex-column">
            <li class="nav-item @if(Request::segment(1)=='SCOA0903') bg-warning @endif">
                <a class="nav-link" aria-current="page" href="/SCOA0903">
                <span data-feather="chevrons-right"></span>
                대기오염통계
                </a>
            </li>
        </ul>
        <ul class="nav flex-column">
            <li class="nav-item @if(Request::segment(1)=='SCOA0904') bg-warning @endif">
                <a class="nav-link" aria-current="page" href="/SCOA0904">
                <span data-feather="chevrons-right"></span>
                오존황사 발생정보
                </a>
            </li>
        </ul>
        <ul class="nav flex-column">
            <li class="nav-item @if(Request::segment(1)=='SCOA0905') bg-warning @endif">
                <a class="nav-link" aria-current="page" href="/SCOA0905">
                <span data-feather="chevrons-right"></span>
                미세먼지 경보 정보
                </a>
            </li>
        </ul> -->
    </div>
</nav> 