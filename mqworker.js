self.addEventListener('push', function(event) {

    console.log('Push message received');

    event.waitUntil(
        fetch('api.php').then(function(response) {
            if (response.status !== 200) {
                // Either show a message to the user explaining the error
                // or enter a generic message and handle the
                // onnotificationclick event to direct the user to a web page
                console.log('Looks like there was a problem. Status Code: ' + response.status);
                throw new Error();
            }

            // Examine the text in the response
            return response.json().then(function(data) {
                if (data.error || !data.notification) {
                    console.error('The API returned an error.', data.error);
                    throw new Error();
                }

                var title = data.notification.title;
                var message = data.notification.message;
                var icon = data.notification.icon;
                var notificationTag = data.notification.tag;

                return self.registration.showNotification(title, {
                    body: message,
                    icon: icon,
                    tag: notificationTag
                });
            });
        }).catch(function(err) {
            console.error('Unable to retrieve data', err);

            var title = 'An error occurred';
            var message = 'We were unable to get the information for this push message';
            var icon = URL_TO_DEFAULT_ICON;
            var notificationTag = 'notification-error';
            return self.registration.showNotification(title, {
                body: message,
                icon: icon,
                tag: notificationTag
            });
        })
    );




});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    var clickResponsePromise = Promise.resolve();
    if (event.notification.data && event.notification.data.url) {
        clickResponsePromise = clients.openWindow(event.notification.data.url);
    }

    event.waitUntil(
        Promise.all([
            clickResponsePromise
        ])
    );
});