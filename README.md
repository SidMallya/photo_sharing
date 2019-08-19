# photo_sharing
A simple photo sharing website using PHP, MySQL and Amazon S3 with option to automatically deploy using AWS CloudFormation.

To deploy this website on AWS automatically using CloudFormation, use the template photo_sharing_cfn_template.json to create a stack by entering the stack name, parameter values and checking the checkbox "I acknowledge that AWS CloudFormation might create IAM resources.". Once stack creation is complete you can view the website URL, RDS endpoint and name of the Amazon S3 bucket in the Outputs tab.

>Note: This template will launch resources without you being charged if you are under free tier.  However, you will incur charges if you have some resources running aleady or exceed the free tier usage limits.  Please use caution while deploying this template if you don't want to be billed.  Once you delete the stack RDS will automatically take a final backup.  Make sure you delete this to ensure you don't get billed.

To deploy this website on AWS manually, follow the below steps:

1. Create an S3 bucket, disable "Block public access" and add the following bucket policy replacing "BucketName" with the actual bucket name:

```json
{
    "Version": "2012-10-17",
    "Id": "Policy1563259243238",
    "Statement": [
        {
            "Sid": "Stmt1563259241991",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::BucketName/*"
        }
    ]
}
```

2. Create an IAM role that has full access to S3 bucket created above.

3. Create an RDS MySQL database instance and allow incoming traffic on port 3306 from the EC2 instance which you are going to launch in the next step.

4. Launch an Amazon Linux EC2 instance with the following user data and the IAM role created in step 2.  The user data takes care of installing Apache, PHP, MySQL, Git and Composer and downloads the code repository on GitHub.  The user data and IAM role can be specified in "Step 3: Configure Instance Details" while launching an EC2 instance.  

```shell
#!/bin/bash -ex
yum -y update
yum install -y httpd24 php70 mysql56-server php70-mysqlnd
yum install git -y
chkconfig httpd on
service httpd start
cd /var/www/html
git clone https://github.com/SidMallya/photo_sharing.git
cd photo_sharing
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php
sudo php -r "unlink('composer-setup.php');"
sudo ./composer.phar require aws/aws-sdk-php
chown -R apache /var/www/html
```

5. SSH into the EC2 instance and update the following details in /var/www/html/photo_sharing/config.php file:
    RDS endpoint
    DB username
    DB password
    S3 bucket name (created in step 1)
    AWS Region

    Note: Default database name is photo_sharing.  If you change this setting, then update init.sql file accordingly.

6. Execute the SQL commands in data/init.sql to create prerequisite database and tables.
