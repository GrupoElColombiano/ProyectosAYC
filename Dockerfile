FROM node as builder

WORKDIR /app

# Add lockfile and package.jsons
COPY ./*.json yarn.lock ./
COPY nuxt.config.ts ./
COPY .yarnrc.yml ./

RUN yarn install

COPY . .

RUN yarn build

EXPOSE 3000

# CMD ["yarn", "dev"]

# production stage
#FROM nginx:stable-perl as production
#
#COPY docker/default.conf /etc/nginx/conf.d/default.conf
#
#COPY --from=builder /build/apps/platform/dist /usr/share/nginx/html
#EXPOSE 80
#
#CMD ["nginx", "-g", "daemon off;"]
