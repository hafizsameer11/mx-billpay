<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard.index') }}" class="sidebar-brand">
            MX-BillPay<span></span>
        </a>
        <div class="sidebar-toggler">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav" id="sidebarNav">
            <!-- Main Section -->
            <li class="nav-item nav-category">Main</li>
            <li class="nav-item">
                <a href="{{ route('dashboard.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="home"></i> <!-- Home icon for Dashboard -->
                    <span class="link-title">Dashboard</span>
                </a>
            </li>

            <!-- Manage Bills Section -->
            <li class="nav-item nav-category">Manage Bills</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails" role="button" aria-expanded="false"
                    aria-controls="emails">
                    <i class="link-icon" data-feather="clipboard"></i> <!-- Clipboard icon for Services -->
                    <span class="link-title">Services</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="emails">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('category.index') }}" class="nav-link">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('billeritem.show') }}" class="nav-link">Services</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('service.provider') }}" class="nav-link">Services Provider</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Bill History Section -->
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails2" role="button" aria-expanded="false"
                    aria-controls="emails2">
                    <i class="link-icon" data-feather="archive"></i> <!-- Archive icon for Bill History -->
                    <span class="link-title">Bill History</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="emails2">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('billPayments.transactions') }}" class="nav-link">All Bills</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pending.billPayments.transactions') }}" class="nav-link">Pending
                                Bills</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('complete.billPayments.transactions') }}" class="nav-link">Complete
                                Bills</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('return.billPayments.transactions') }}" class="nav-link">Return Bills</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- User Panel -->
            <li class="nav-item nav-category">User Panel</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails3" role="button" aria-expanded="false"
                    aria-controls="emails3">
                    <i class="link-icon" data-feather="users"></i> <!-- Users icon for Manage User -->
                    <span class="link-title">Manage User</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="emails3">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('user.index') }}" class="nav-link">All Users</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Transactions Section -->
            <li class="nav-item nav-category">Transactions</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails1" role="button" aria-expanded="false"
                    aria-controls="emails1">
                    <i class="link-icon" data-feather="credit-card"></i> <!-- Credit Card icon for Transactions -->
                    <span class="link-title">Transactions</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="emails1">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('pending.transactions') }}" class="nav-link">Payment Request</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('completed.transactions') }}" class="nav-link">Payment Log</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('all.transactions') }}" class="nav-link">Transactions List</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Slides Section -->
            <li class="nav-item nav-category">Slides</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#slides" role="button" aria-expanded="false"
                    aria-controls="slides">
                    <i class="link-icon" data-feather="image"></i> <!-- Image icon for Slides -->
                    <span class="link-title">Manage Slides</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="slides">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('slides.index') }}" class="nav-link">All Slides</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('slides.create') }}" class="nav-link">Add Slide</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Faqs Section -->
            <li class="nav-item nav-category">Faqs</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails6" role="button" aria-expanded="false"
                    aria-controls="emails6">
                    <i class="link-icon" data-feather="help-circle"></i> <!-- Help icon for Faqs -->
                    <span class="link-title">Faqs</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="emails6">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('faq.category') }}" class="nav-link">Faq Category</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('faq.addFaqs') }}" class="nav-link">Add Faqs</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('faq.show') }}" class="nav-link">Faqs</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- SMTP Section -->
            <li class="nav-item nav-category">Settings</li>
            <li class="nav-item">
                <a href="{{ route('smtp.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="settings"></i> <!-- Settings icon for SMTP -->
                    <span class="link-title">SMTP</span>
                </a>
            </li>

            <!-- Access Token Section -->
            <li class="nav-item nav-category">Access Token</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#emails7" role="button" aria-expanded="false"
                    aria-controls="emails7">
                    <i class="link-icon" data-feather="key"></i> <!-- Key icon for Access Tokens -->
                    <span class="link-title">Access Token</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="emails7">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('AccessToken') }}" class="nav-link">Access Tokens</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('addToken') }}" class="nav-link">Add Access Token</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Social Media Section -->
            <li class="nav-item nav-category">Social Media</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#uiComponents" role="button"
                    aria-expanded="false" aria-controls="uiComponents">
                    <i class="link-icon" data-feather="share-2"></i> <!-- Share icon for Social Media -->
                    <span class="link-title">Social Media</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="uiComponents">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('social.media.index') }}" class="nav-link">Social Media</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('social.media.create') }}" class="nav-link">Add Social Media</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item nav-category">Chat</li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#chatComponents" role="button"
                    aria-expanded="false" aria-controls="chatComponents">
                    <i class="link-icon" data-feather="message-square"></i> <!-- Message icon for Chat -->
                    <span class="link-title">Chat</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" data-bs-parent="#sidebarNav" id="chatComponents">
                    <ul class="nav sub-menu">
                        <li class="nav-item">
                            <a href="{{ route('chat.index') }}" class="nav-link">All Chats</a>
                        </li>
                        
                    </ul>
                </div>
            </li>

        </ul>
    </div>
</nav>
