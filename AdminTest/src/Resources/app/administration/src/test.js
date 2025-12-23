console.log("lol")

const user = {
    name: "Jakob",
    email: "jlass@allnet.at",
}

const test = async (user) => {
    console.log("1")
    const a =  await user.name;
    console.log("1.5");
    const b =  await user.email;
    const c = Promise.all([a,b])
    console.log("2")
    return c
}
console.log("3")
test(user).then(console.log)
console.log("4")