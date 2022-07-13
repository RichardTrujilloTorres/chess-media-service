FROM ubuntu:18.04

RUN apt-get update && \
    apt-get install -y git curl build-essential
RUN curl https://sh.rustup.rs -sSf | bash -s -- -y
ENV PATH="/root/.cargo/bin:${PATH}"

WORKDIR /usr/src
RUN git clone https://github.com/lichess-org/lila-gif .

RUN cargo install --path .

EXPOSE 6175

CMD ./target/release/lila-gif --bind 0.0.0.0:6175
