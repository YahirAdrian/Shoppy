export default function guestCredentials(emailFieldId, passwordFieldId) {
    return {
        open: false,
        fill(email, password) {
            document.getElementById(emailFieldId).value = email;
            document.getElementById(passwordFieldId).value = password;
            this.open = false;
        },
    };
}
