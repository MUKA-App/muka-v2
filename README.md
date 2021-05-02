# MUKA

## Installing for development
_Requires docker, docker-compose, node.js, yarn_ 

## Clone the repo
```bash
git clone https://github.com/MUKA-App/muka-v2.git
```
## Generate SSL certificate for development over HTTPS
1. Install OpenSSL globally.
2. Ensure you are within the root directory of the cloned MUKA repo (on the same level as `composer.json` and `docker-compose.yml`).
3. Run the following command to generate keys in the required directory. You can press enter when prompted to use the default values declared in the config files:

### On Mac run:
```bash
openssl req -config docker/apache/config/openssl_mac.conf -new -sha256 -newkey rsa:2048 \
-nodes -keyout docker/apache/config/muka.local.key -x509 -days 1062 \
-out docker/apache/config/muka.local.crt
 ```

#### Once the cert has been generated we need to make your machine trust it:
1. Open `Keychain Access` on your Mac.
2. Choose a keychain (e.g `login`).
3. Select the `Certificates` category.
4. Click the `+` icon (or `File->Import items`) and import the newly created file located:
`muka/docker/apache/config/muka.local.crt`.
5. Once added: 
    - Double click the certificate
    - Open the `Trust` dropdown
    - Select `Always trust`
    
### On Ubuntu/Debian:
Creating a self-signed certificate on Ubuntu is a bit trickier than on Mac. You will need to create a CA (Certificate 
Authority) to sign your certificates.

Go to your root level of your computer by running `cd`.

Run the following command:
```bash
openssl genrsa -des3 -out rootCA.key 4096
```

After running this you will be asked to provide a pass phrase for the rootKey. Using something unique is recommended.

Make sure to note this pass phrase down as it will be required further down in the process, then run this:
```bash
openssl req -x509 -new -nodes -key rootCA.key -sha256 -days 1024 -out rootCA.crt
```

Feel free to change the default values for the root certificate. Important that value of the common name (CN) 
has to be `muka.local`

After completing the above step run:
```bash
openssl genrsa -out muka.local.key 2048
```

This will generate a public key. After this run this:

Make sure to chane `<your_route>` with the path to the actual `muka-v2` folder

```bash
openssl req -new -key muka.local.key \
-addext "subjectAltName = DNS.1:muka.local, DNS.2:localhost, DNS.3:127.0.0.1" \
-config <your_route>/muka-v2/docker/apache/config/openssl_ubuntu.conf -out muka.local.csr
```
This will create a signature for the certificate.

After that generate the cert by running this:

```bash
openssl x509 -req -in muka.local.csr -CA rootCA.crt -CAkey rootCA.key -CAcreateserial -out muka.local.crt -days 500 -sha256
```

Certificate is now ready to be used. Now run the following commands:

From still the root of your filesystem (this is where the cert should be). Replace `<your_route>` with the actual path
```bash
sudo cp muka.local.crt /usr/local/share/ca-certificates/
cp muka.local.crt <your_route>/muka-v2/docker/apache/config/
cp muka.local.key <your_route>/muka-v2/docker/apache/config/
sudo update-ca-certificates
```

After adding the certificate to your trusted sources, go ahed and add your `rootCA.crt` to your preferred browser as a 
CA.
    
### Finishing (both Mac and Ubuntu/Debian)
If you have pre-existing docker containers, we need to update some things
1. Run `./dev build apache` to rebuild the container with the new config
2. Ensure the `APP_URL` env variable is over `https://` instead of `http://`.

### Create .env file from example
```bash
 cp .env.example .env
```

### Build and start the containers using docker-compose
```bash
./dev build

./dev up
```

### Insert APP_SECRET
Ask the developers for the APP_SECRET environment variable so that your local copy will work.

Once the installation steps are complete add `127.0.0.1 muka.local` to your hosts file via:
```bash
sudo nano /etc/hosts
```
The application will be available at [muka.local](https://muka.local)

## Running commands
Commands are executed against the docker containers via `./dev`

Available commands are `up`, `down`, `build`, `composer`, `artisan` , `phpunit`, `cs` (codesniffer), `larastan` and `npm`

```bash
# e.g installing composer dependencies 
./dev composer install

# e.g clearing artisan cache
./dev artisan cache:clear

# e.g running codesniffer with diff reporting
./dev cs --report=diff

# e.g running larastan
./dev larastan

# e.g install frontend packages
./dev npm install
```

## Running locally
Assuming the containers are built but not running, navigate to your cloned directory and run:

```bash
# Initialise the container
./dev up

# Install dependencies and run migrations
./dev composer install

# Run migrations
./dev artisan migrate

# Install frontend dependencies
./dev npm install

# Build frontend files
./dev npm run dev
```

In order to take down the containers call:
```bash
./dev down
```