#!/bin/bash

# Script to fix S3 bucket permissions for public access
# Run this to make onboarding documents publicly accessible

echo "Fixing S3 bucket permissions for public access..."

# Check if S3 is configured
if [ -z "$AWS_BUCKET" ] || [ -z "$AWS_KEY" ] || [ -z "$AWS_SECRET" ]; then
    echo "Error: S3 credentials not configured"
    exit 1
fi

# Install AWS CLI if not present
if ! command -v aws &> /dev/null; then
    echo "Installing AWS CLI..."
    curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
    unzip awscliv2.zip
    sudo ./aws/install
fi

# Configure AWS CLI
aws configure set aws_access_key_id "$AWS_KEY"
aws configure set aws_secret_access_key "$AWS_SECRET"
aws configure set default.region "${AWS_REGION:-us-east-1}"

echo "Setting bucket policy for public read access..."

# Create bucket policy for public read access
cat > /tmp/bucket-policy.json <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadGetObject",
      "Effect": "Allow",
      "Principal": "*",
      "Action": "s3:GetObject",
      "Resource": "arn:aws:s3:::${AWS_BUCKET}/uploads/onboarding/*"
    }
  ]
}
EOF

# Apply bucket policy
aws s3api put-bucket-policy --bucket "$AWS_BUCKET" --policy file:///tmp/bucket-policy.json

echo "Setting CORS configuration..."

# Create CORS configuration
cat > /tmp/cors-config.json <<EOF
{
  "CORSRules": [
    {
      "AllowedOrigins": ["*"],
      "AllowedMethods": ["GET", "HEAD"],
      "AllowedHeaders": ["*"],
      "MaxAgeSeconds": 3000
    }
  ]
}
EOF

# Apply CORS configuration
aws s3api put-bucket-cors --bucket "$AWS_BUCKET" --cors-configuration file:///tmp/cors-config.json

echo "Setting ACL for uploaded files..."

# Set ACL for existing files
aws s3 sync s3://${AWS_BUCKET}/uploads/onboarding/ s3://${AWS_BUCKET}/uploads/onboarding/ --acl public-read --exclude "*" --include "*.jpg" --include "*.png" --include "*.pdf" --include "*.jpeg"

echo ""
echo "✓ S3 permissions updated successfully!"
echo ""
echo "Files should now be accessible at:"
echo "https://${AWS_BUCKET}.s3.${AWS_REGION:-us-east-1}.amazonaws.com/uploads/onboarding/filename.jpg"

