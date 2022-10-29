import Event from './event';

Echo.join('chat.2')
    .here(users => {
        Event.$emit('users.here', users);
    })
    .joining(user => {
        Event.$emit('users.joined', user);
    })
    .leaving(user => {
        Event.$emit('users.left', user);
    })


