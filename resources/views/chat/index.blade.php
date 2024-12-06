@extends('layouts.main')

@section('main')
    <style>
        .attachment-img {
            max-width: 100px;
            max-height: 100px;
            margin-top: 5px;
            display: block;
        }

        .bubble img {
            max-width: 100%;
            width: 200px;
            height: auto;
            border-radius: 5px;
        }

        .bubble a {
            text-decoration: underline;
            font-weight: bold;
            color: #007bff;
        }
    </style>
    <div class="row chat-wrapper">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row position-relative">
                        <div class="col-lg-4 chat-aside border-end-lg">
                            <div class="aside-content">
                                <div class="aside-header">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-2">
                                        <div class="d-flex align-items-center">
                                            <figure class="me-2 mb-0">
                                                <img src="https://via.placeholder.com/43x43" class="img-sm rounded-circle"
                                                    alt="profile">
                                                <div class="status online"></div>
                                            </figure>
                                            <div>
                                                <h6>Amiah Burton</h6>
                                                <p class="text-secondary fs-13px">Software Developer</p>
                                            </div>
                                        </div>
                                    </div>
                                    <form class="search-form">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="searchForm"
                                                placeholder="Search here...">
                                            <span class="input-group-text bg-transparent">
                                                <i data-feather="search" class="cursor-pointer"></i>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                                <div class="aside-body">
                                    <div class="tab-content mt-3">
                                        <div class="tab-pane fade show active" id="chats" role="tabpanel"
                                            aria-labelledby="chats-tab">
                                            <div>
                                                <p class="text-secondary mb-1">Recent chats</p>
                                                <ul class="list-unstyled chat-list px-1">
                                                    @foreach ($users as $user)
                                                        <li class="chat-item pe-1">
                                                            <a href="{{ route('chat.show', ['id' => $user['id']]) }}"
                                                                class="d-flex align-items-center">
                                                                <figure class="mb-0 me-2">
                                                                    <img src="@if (isset($user['profile_picture'])) {{ $user['profile_picture'] }}
                      @else
                      https://via.placeholder.com/37x37 @endif"
                                                                        class="img-xs rounded-circle" alt="user">
                                                                    <div class="status online"></div>
                                                                </figure>
                                                                <div
                                                                    class="d-flex justify-content-between flex-grow-1 border-bottom">
                                                                    <div>
                                                                        <p class="text-body fw-bolder">{{ $user['name'] }}
                                                                        </p>
                                                                        <p class="text-secondary fs-13px">
                                                                            {{ $user['email'] }}</p>
                                                                    </div>
                                                                    <div class="d-flex flex-column align-items-end">
                                                                        <p class="text-secondary fs-13px mb-1">4:32 PM</p>
                                                                        <div class="badge rounded-pill bg-primary ms-auto">0
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    @endforeach

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 chat-content">
                            <div class="chat-header border-bottom pb-2">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="corner-up-left" id="backToChatList"
                                            class="icon-lg me-2 ms-n2 text-secondary d-lg-none"></i>
                                        <figure class="mb-0 me-2">
                                            <img src="https://via.placeholder.com/43x43" class="img-sm rounded-circle"
                                                alt="image">
                                            <div class="status online"></div>
                                        </figure>
                                        <div>
                                            <p>{{ $firstUser->account->firstName }}</p>
                                            <p class="text-secondary fs-13px">{{ $firstUser->account->lastName }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="chat-body">
                                <button id="loadEarlierButton" class="btn btn-secondary btn-sm mb-3">Load Earlier</button>
                                <ul class="messages">
                                    @foreach ($messages as $message)
                                        @if ($message->sender === 'admin')
                                            <!-- Admin message (me) -->
                                            <li class="message-item me" data-time="{{ $message->created_at }}">
                                                <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                    alt="avatar">
                                                <div class="content">
                                                    <div class="message">
                                                        <div class="bubble">
                                                            @if ($message->attachment)
                                                                <!-- If the attachment is an image -->
                                                                @if (in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <img src="{{ asset('storage/' . $message->attachment) }}"
                                                                        alt="attachment" class="img-fluid rounded mb-2">
                                                                @else
                                                                    <!-- If the attachment is a file -->
                                                                    <a href="{{ asset('storage/' . $message->attachment) }}"
                                                                        target="_blank" class="text-primary">
                                                                        Download Attachment
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            <p>{{ $message->message }}</p>
                                                        </div>
                                                        <span>{{ $message->created_at->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        @else
                                            <!-- User message (friend) -->
                                            <li class="message-item friend" data-time="{{ $message->created_at }}">
                                                <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle"
                                                    alt="avatar">
                                                <div class="content">
                                                    <div class="message">
                                                        <div class="bubble">
                                                            @if ($message->attachment)
                                                                <!-- If the attachment is an image -->
                                                                @if (in_array(pathinfo($message->attachment, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <img src="{{ asset('storage/' . $message->attachment) }}"
                                                                        alt="attachment" class="img-fluid rounded mb-2">
                                                                @else
                                                                    <!-- If the attachment is a file -->
                                                                    <a href="{{ asset('storage/' . $message->attachment) }}"
                                                                        target="_blank" class="text-primary">
                                                                        Download Attachment
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            <p>{{ $message->message }}</p>
                                                        </div>
                                                        <span>{{ $message->created_at->format('h:i A') }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach

                                </ul>
                            </div>

                            <div class="chat-footer d-flex">
                                <form id="messageForm" class="d-flex w-100">
                                    @csrf
                                    <input type="hidden" name="user_id" id="currentUserId" value="{{ $firstUser->id }}">
                                    <input type="text" id="messageInput" name="message"
                                        class="form-control rounded-pill me-2" placeholder="Type a message">
                                    <input type="file" id="attachmentInput" name="attachment"
                                        class="form-control-file d-none">
                                    <label for="attachmentInput" class="btn border btn-icon rounded-circle me-2"
                                        title="Attach file">
                                        <i data-feather="paperclip"></i>
                                    </label>
                                    <button type="submit" class="btn btn-primary btn-icon rounded-circle">
                                        <i data-feather="send"></i>
                                    </button>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additonal-script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Load earlier messages
            $('#loadEarlierButton').on('click', function() {
                const userId = $('#currentUserId').val();
                const oldestMessageTime = $('.messages li:first').data('time');

                $.ajax({
                    url: '/api/load-earlier-messages', // Update with the correct API route
                    type: 'POST',
                    data: {
                        user_id: userId,
                        last_message_time: oldestMessageTime,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            const messages = Object.values(response.data);
                            messages.forEach((message) => {
                                const messageHtml = `
                        <li class="message-item ${message.sender === 'admin' ? 'me' : 'friend'}" data-time="${message.created_at}">
                            <div class="content">
                                <div class="message">
                                    <div class="bubble">
                                        <p>${message.message || ''}</p>
                                        ${
                                            message.attachment
                                                ? `<a href="${message.attachment}" target="_blank">
                                                                    <img src="${message.attachment}" alt="attachment" class="attachment-img">
                                                                   </a>`
                                                : ''
                                        }
                                    </div>
                                    <span>${message.created_at}</span>
                                </div>
                            </div>
                        </li>`;
                                $('.messages').prepend(messageHtml);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('An error occurred while loading earlier messages.');
                    }
                });
            });


            // Handle message form submission
            $('#messageForm').on('submit', function(e) {
                e.preventDefault();

                const userId = $('#currentUserId').val();
                const message = $('#messageInput').val();
                const attachment = $('#attachmentInput')[0].files[0]; // Get the file

                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('message', message);
                //append the csrf token
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                if (attachment) {
                    formData.append('attachment', attachment);
                }

                $.ajax({
                    url: '/api/admin/messages', // Update with the correct API route
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            const {
                                message,
                                attachment
                            } = response.data;

                            const messageHtml = `
                    <li class="message-item me">
                        <img src="https://via.placeholder.com/36x36" class="img-xs rounded-circle" alt="avatar">
                        <div class="content">
                            <div class="message">
                                <div class="bubble">
                                    <p>${message || ''}</p>
                                    ${
                                        attachment
                                            ? `<a href="${attachment}" target="_blank">
                                                                    <img src="${attachment}" alt="attachment" class="attachment-img">
                                                                   </a>`
                                            : ''
                                    }
                                </div>
                                <span>${message.formated_time}</span>
                            </div>
                        </div>
                    </li>`;
                            $('.messages').append(messageHtml);

                            // Clear inputs
                            $('#messageInput').val('');
                            $('#attachmentInput').val('');
                        } else {
                            alert('Failed to send the message. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('An error occurred while sending the message.');
                    }
                });
            });


            // Poll for new messages
            setInterval(function() {
                const userId = $('#currentUserId').val();
                const lastMessageTime = $('.messages li:last').data('time');

                $.ajax({
                    url: '/api/new-messages-admin', // Your API endpoint
                    type: 'POST',
                    data: {
                        user_id: userId,
                        last_message_time: lastMessageTime,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            const messages = response.data;

                            messages.forEach((message) => {
                                const attachmentHtml = message.attachment ?
                                    `
                            <a href="${message.attachment}" target="_blank">
                                ${
                                    message.attachment.endsWith('.jpg') ||
                                    message.attachment.endsWith('.jpeg') ||
                                    message.attachment.endsWith('.png') ||
                                    message.attachment.endsWith('.gif')
                                        ? `<img src="${message.attachment}" alt="attachment" class="attachment-img">`
                                        : `<span class="attachment-file">Download Attachment</span>`
                                }
                            </a>
                          ` :
                                    '';

                                const messageHtml = `
                        <li class="message-item ${message.sender === 'admin' ? 'me' : 'friend'}" data-time="${message.created_at}">
                            <div class="content">
                                <div class="message">
                                    <div class="bubble">
                                        <p>${message.message || ''}</p>
                                        ${attachmentHtml}
                                    </div>
                                    <span>${message.created_at}</span>
                                </div>
                            </div>
                        </li>`;

                                $('.messages').append(messageHtml);
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    },
                });
            }, 1500); // Poll every 5 seconds

        });
    </script>
@endsection
