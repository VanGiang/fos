VOTE = 5;
RE_VOTE = 6;
INVITE = 7;
COMMENT = 8;
REPLY_COMMENT = 9;

$(function() {
    checkWindowWidth();
    $(window).resize(function() {
        checkWindowWidth();
    });
    notificationDropDown();
    show_bubble();
});

function checkWindowWidth() {
    var width = $(window).width();
    if (width < 1100) {
        $('#main-content').width('100%');
        $('#stream').hide();
    } else {
        $('#main-content').width('75%');
        $('#stream').show();
    }
}

function show_bubble(only_new) {
    if (only_new === true) {
        var user_class = '.info_user_new';
        var poll_class = '.info_poll_new';
    } else {
        var user_class = '.info_user';
        var poll_class = '.info_poll';
    }
    var options = {
        position: 'bottom',
        align: 'center',
        innerHtmlStyle: {
            color: '#FFFFFF',
            'text-align': 'center'
        },
        themeName: 'all-black',
        themePath: 'images/jquerybubblepopup-themes',
        openingDelay: 1000
    };
    
    $(user_class).CreateBubblePopup(options);
    $(user_class).hover(function() {
        var profile_id = $(this).attr('data-profile_id');
        showInfoUser(profile_id, user_class);
    }, function() {
    });
    
    $(poll_class).CreateBubblePopup(options);
    $(poll_class).hover(function() {
       var poll_id = $(this).attr('data-poll_id');
       showInfoPoll(poll_id, poll_class);
    }, function() {
    });
}

function showInfoUser(profile_id, user_class) {
    if (localStorage.getItem('profile'+profile_id)) {
        var html = localStorage.getItem('profile'+profile_id);
        $(user_class).SetBubblePopupInnerHtml(html);
    } else {
        var url = 'index.php?r=profile/getInfo';
        console.log(profile_id);
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                profile_id: profile_id
            }
        }).success(function(msg) {
            var profile = jQuery.parseJSON(msg);
            var employee_code = profile.employee_code;
            var position = profile.position;
            var email = profile.email;
            var phone = profile.phone;
            var address = profile.address;
            var date_of_birth = profile.date_of_birth;
            var inner_html = 'Employee code: '+ employee_code +'<br/> Email: '+ email;
            if (position) {
                inner_html = inner_html + '<br/> Position: '+ position;
            }
            if (phone) {
                inner_html = inner_html +'<br/> Phone: '+ phone;
            }
            if (date_of_birth) {
                inner_html = inner_html +'<br/> Birth day: '+ date_of_birth;
            }
            if (address) {
                inner_html = inner_html +'<br/> Address: '+ address;
            }
            $(user_class).SetBubblePopupInnerHtml(inner_html);
            localStorage.setItem('profile'+profile_id, inner_html);
        }).fail(function() {});
    }
        
}

function showInfoPoll(poll_id, poll_class) {
    if (localStorage.getItem('poll'+poll_id)) {
        var html = localStorage.getItem('poll'+poll_id);
        $(poll_class).SetBubblePopupInnerHtml(html);
    } else {
        var url = 'index.php?r=poll/getInfo';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                poll_id: poll_id
            }
        }).success(function(msg) {
            var poll = jQuery.parseJSON(msg);
            var inner_html = 'Description: '+poll.description +'<br/>'+
                'Start at: '+poll.start_at +'<br/>'+
                'End at: '+poll.end_at;
            $(poll_class).SetBubblePopupInnerHtml(inner_html);
            localStorage.setItem('poll'+poll_id, inner_html);
        }).fail(function() {
        });
    }
}
function notificationDropDown() {
    loading_image = new HtmlElement('loading', {id: 'notification'});
    $('.notification-menu').CreateBubblePopup({
        width: '250px',
        innerHtml: '<img id="loading-notification" src="images/ajax-loader-circle.gif">',
        themeName: 'blue',
        themePath: 'images/jquerybubblepopup-themes',
        selectable: true,
        divStyle: {
            'margin-top': '5px'
        },
        align: 'center',
        manageMouseEvents: false
    });
    $('.notification-menu').click(function() {
        if ($(this).IsBubblePopupOpen()) {
            $(this).HideBubblePopup();
        } else {
            if (sessionStorage.getItem('is_new_notify') == null
                    || sessionStorage.getItem('is_new_notify') == 'true') {
                loadNotification();
                sessionStorage.setItem('is_new_notify', 'false');
            } else {
                $('.notification-menu').SetBubblePopupInnerHtml(sessionStorage.getItem('loaded_notify'));
            }
            $(this).ShowBubblePopup();
        }
    });
}

function loadNotification() {
    var url = 'index.php?r=notification/getnotify';
    $.ajax({
        type: 'GET',
        url: url,
    }).success(function(msg) {
        var all_notify = '';
        var packet = $.parseJSON(msg);
        if (packet.length) {
            $.each($.parseJSON(msg), function(index, notify) {
                var txt = createNotifyText(notify.activities);
                var notify_html = new HtmlElement('notify_dropdown', {txt: txt, viewed: notify.viewed, poll_id: notify.poll_id});
                all_notify += notify_html.html;
            });
            all_notify += '<a href="index.php?r=notification/index"><div> See all notifications.</div></a>';
            $('.notification-menu').SetBubblePopupInnerHtml(all_notify);
        } else {
            all_notify = 'No notification.';
            $('.notification-menu').SetBubblePopupInnerHtml(all_notify);
        }
        addNotifyListenner();
        sessionStorage.setItem('loaded_notify', all_notify);
    }).fail(function() {
    });
}

function createNotifyText(data) {
    var voters = [];
    var commenters = [];
    var inviter;
    var notify_txt = '';
    var activity;
    var total_cmt = 0;

    var activities = [];
    $.each(data, function(index, act) {
        activity = $.parseJSON(act);
        if (activity.type == VOTE || activity.type == RE_VOTE) {
            if (voters.indexOf(activity.profile_name) == -1) {
                voters.push(activity.profile_name);
            }
        } else if (activity.type == INVITE) {
            inviter = activity.profile_name;
        } else if (activity.type == COMMENT || activity.type == REPLY_COMMENT) {
            total_cmt++;
            if (commenters.indexOf(activity.profile_name) == -1) {
                commenters.push(activity.profile_name);
            }
        }
    });

    if (voters.length == 1) {
        notify_txt += '<b>' + voters[0] + '</b> voted ';
    } else if (voters.length == 2) {
        notify_txt += '<b>' + voters[0] + '</b> and <b>' + voters[1] + '</b> voted ';
    } else if (voters.length > 2) {
        notify_txt += '<b>' + voters[0] + '</b> and ' + (voters.length - 1) + ' others voted ';
    }
    if (voters.length && commenters.length) {
        notify_txt += ', ';
    }
    if (commenters.length == 1) {
        notify_txt += '<b>' + commenters[0] + '</b> wrote ' + total_cmt + (total_cmt > 1 ? ' comments ' : ' comment ');
    } else if (commenters.length == 2) {
        notify_txt += '<b>' + commenters[0] + '</b> and <b>' + commenters[1] + '</b> wrote ' + total_cmt + ' comments ';
    } else if (commenters.length > 2) {
        notify_txt += '<b>' + commenters[0] + '</b> and ' + (commenters.length - 1) + ' wrote ' + total_cmt + ' comments ';
    }

    if (inviter) {
        notify_txt = '<b>' + inviter + '</b> has invited you to vote '
    }

    notify_txt += 'in your poll <b>' + activity.poll_question + '</b>';
    return notify_txt;
}

function addNotifyListenner() {
    $('.notify_element').click(function() {
        var poll_id = $(this).attr('data-poll_id');
        window.location.href = 'index.php?r=poll/view&id=' + poll_id;
    });
}