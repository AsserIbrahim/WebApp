/*document.addEventListener('DOMContentLoaded', () => {
    let form = document.querySelector('#form')
    console.log(form)
    form.addEventListener('submit', (event) => {
        event.preventDefault()
        let first_name = document.querySelector('#first_name').value
        console.log(first_name)
        let last_name = document.querySelector('#last_name').value
        let DOB = document.querySelector('#DOB').value
        let student_email = document.querySelector('#student_email').value
        let program = document.querySelector('#program').value
        console.log("I work")

        //window.location.href = 'profile.php'

        // document.querySelector('#pfirst_name').value = first_name;
        // document.querySelector('#last_name').value = last_name;
        // document.querySelector('#DOB').value = DOB;
        // document.querySelector('#student_email').value = student_email;
        // document.querySelector('#program').value = program;
    })
});*/

const redirect = () => {
    window.location.href = 'profile.php';
}
